<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    private function mockGoogleUser(string $email, string $id): void
    {
        $googleUser = (new GoogleUser)->map([
            'id' => $id,
            'name' => 'UPTM Student',
            'email' => $email,
        ]);
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
    }

    public function test_different_students_can_register_and_return_to_their_own_accounts(): void
    {
        foreach (['first', 'second', 'third'] as $student) {
            $this->mockGoogleUser($student.'@student.uptm.edu.my', 'google-'.$student);
            $this->get(route('google.callback'))->assertRedirect(route('student.dashboard'));
            $user = User::where('user_email', $student.'@student.uptm.edu.my')->firstOrFail();
            $this->assertAuthenticatedAs($user);
            $this->assertSame('student', $user->user_role);
            $this->assertNotEmpty($user->password);
            $this->post(route('logout'))->assertRedirect('/');
            Auth::forgetGuards();
        }

        $first = User::where('user_email', 'first@student.uptm.edu.my')->firstOrFail();
        $password = $first->password;
        $this->mockGoogleUser('first@student.uptm.edu.my', 'google-first');
        $this->get(route('google.callback'))->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($first);
        $this->assertDatabaseCount('users', 3);
        $this->assertSame($password, $first->fresh()->password);
    }

    public function test_students_can_register_and_return_without_a_password_column(): void
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('password');
        });

        $this->mockGoogleUser('student@student.uptm.edu.my', 'google-student');
        $this->get(route('google.callback'))->assertRedirect(route('student.dashboard'));
        $user = User::where('user_email', 'student@student.uptm.edu.my')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $user->getKey(), 'audit_action' => 'login']);

        $this->post(route('logout'))->assertRedirect('/');
        Auth::forgetGuards();
        $this->mockGoogleUser('student@student.uptm.edu.my', 'google-student');
        $this->get(route('google.callback'))->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_student_email_is_normalized(): void
    {
        $this->mockGoogleUser(' Student@STUDENT.UPTM.EDU.MY ', 'google-student');
        $this->get(route('google.callback'))->assertRedirect(route('student.dashboard'));
        $this->assertDatabaseHas('users', ['user_email' => 'student@student.uptm.edu.my', 'user_role' => 'student']);
    }

    public function test_unrelated_email_domain_is_rejected(): void
    {
        $this->mockGoogleUser('outsider@gmail.com', 'google-outsider');
        $this->get(route('google.callback'))->assertRedirect(route('login'))->assertSessionHas('error');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_suspended_student_cannot_log_in(): void
    {
        User::factory()->create(['user_email' => 'student@student.uptm.edu.my', 'user_role' => 'student', 'user_suspended' => true]);
        $this->mockGoogleUser('student@student.uptm.edu.my', 'google-student');
        $this->get(route('google.callback'))->assertRedirect(route('login'))->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_login_requests_google_account_chooser(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('with')->with(['prompt' => 'select_account'])->once()->andReturnSelf();
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/'));
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $this->get(route('google.login'))->assertRedirect('https://accounts.google.com/');
    }
}
