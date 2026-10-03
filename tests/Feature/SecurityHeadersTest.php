<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_headers_cover_pages_redirects_json_and_errors_without_changing_access(): void
    {
        $this->assertSecurityHeaders($this->get('/')->assertOk());
        $this->assertSecurityHeaders($this->get('/student/dashboard')->assertRedirect('/'));
        $this->assertSecurityHeaders($this->get('/missing-security-test-page')->assertNotFound());

        $student = User::factory()->create(['user_role' => 'student']);
        $this->actingAs($student);
        $this->assertSecurityHeaders($this->get('/student/dashboard')->assertOk());
        $this->assertSecurityHeaders($this->getJson(route('student.messages.unread-count'))->assertOk());
        $this->assertSecurityHeaders($this->get('/mpp/dashboard')->assertForbidden());

        $admin = User::factory()->create(['user_role' => 'mpp']);
        $this->actingAs($admin);
        $this->assertSecurityHeaders($this->get('/mpp/dashboard')->assertOk());
        $this->assertSecurityHeaders($this->get('/student/dashboard')->assertForbidden());
    }

    public function test_http_session_is_httponly_and_lax_without_requiring_https(): void
    {
        config(['session.secure' => null]);
        $response = $this->get('http://192.168.0.199:8000/')->assertOk();
        $session = $this->cookie($response, config('session.cookie'));

        $this->assertTrue($session->isHttpOnly());
        $this->assertFalse($session->isSecure());
        $this->assertSame('lax', $session->getSameSite());
        $this->assertFalse($this->cookie($response, 'XSRF-TOKEN')->isHttpOnly());
    }

    public function test_https_automatically_marks_session_and_csrf_cookies_secure(): void
    {
        config(['session.secure' => null]);
        $response = $this->get('https://rental.example.test/')->assertOk();

        $this->assertTrue($this->cookie($response, config('session.cookie'))->isSecure());
        $this->assertTrue($this->cookie($response, config('session.cookie'))->isHttpOnly());
        $this->assertSame('lax', $this->cookie($response, config('session.cookie'))->getSameSite());
        $this->assertTrue($this->cookie($response, 'XSRF-TOKEN')->isSecure());
    }

    public function test_explicit_secure_cookie_setting_is_respected(): void
    {
        config(['session.secure' => true]);
        $response = $this->get('http://rental.example.test/')->assertOk();

        $this->assertTrue($this->cookie($response, config('session.cookie'))->isSecure());
    }

    public function test_google_redirect_contains_only_the_standard_redirect_body(): void
    {
        // Synthetic provider credentials: generating the authorization URL does
        // not contact Google and never reads or displays real OAuth secrets.
        $testSecret = bin2hex(random_bytes(32));
        config(['services.google' => [
            'client_id' => 'security-test-client',
            'client_secret' => $testSecret,
            'redirect' => 'http://192.168.0.199:8000/auth/google/callback',
        ]]);

        $response = $this->get('/auth/google')->assertStatus(302);
        $target = $response->headers->get('Location');
        $this->assertSame('accounts.google.com', parse_url($target, PHP_URL_HOST));
        parse_str(parse_url($target, PHP_URL_QUERY), $query);
        $this->assertSame('security-test-client', $query['client_id']);
        $this->assertSame('code', $query['response_type']);
        $this->assertSame('select_account', $query['prompt']);
        $this->assertArrayNotHasKey('client_secret', $query);
        $this->assertArrayNotHasKey('access_token', $query);
        $this->assertSame((new RedirectResponse($target))->getContent(), $response->getContent());
        $response->assertDontSee($testSecret);
        $this->assertSecurityHeaders($response);
    }

    public function test_user_agent_changes_do_not_bypass_authentication(): void
    {
        foreach (['Mozilla/5.0', 'Googlebot', 'curl/8.0', 'SecurityReview'] as $agent) {
            $this->withHeader('User-Agent', $agent)->get('/student/dashboard')->assertRedirect('/');
            $this->withHeader('User-Agent', $agent)->get('/mpp/dashboard')->assertRedirect('/');
            $this->assertGuest();
        }
    }

    private function cookie(TestResponse $response, string $name): Cookie
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie;
            }
        }

        $this->fail('Expected response cookie was not set: '.$name);
    }

    private function assertSecurityHeaders(TestResponse $response): void
    {
        $response->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $policy = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString('https://cdn.tailwindcss.com', $policy);
        $this->assertStringContainsString('https://fonts.googleapis.com', $policy);
        $this->assertStringContainsString('https://fonts.gstatic.com', $policy);
        $this->assertStringContainsString('https://cdnjs.cloudflare.com', $policy);
        $this->assertStringNotContainsString('upgrade-insecure-requests', $policy);
        $this->assertStringNotContainsString("'unsafe-eval'", $policy);
    }
}
