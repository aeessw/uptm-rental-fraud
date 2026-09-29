<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Helpers\AuditLogger;

class ListingController extends Controller
{

    // =========================================================
    // INDEX - Show active, available listings
    // =========================================================

    public function index(Request $request)
    {
        $query = Listing::visibleTo(Auth::user())->where('listing_status', 'active')->where('listing_availability', 'available')->with('photos');

        $request->validate(['pax' => ['nullable', \Illuminate\Validation\Rule::in(['1', '2', '3', '4', '5+'])]]);
        if ($request->filled('pax')) {
            if ($request->input('pax') === '5+') {
                $query->where('pax', '>=', 5);
            } else {
                $query->where('pax', (int) $request->input('pax'));
            }
        }

        // Search by title or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('listing_title', 'like', '%' . $search . '%')
                  ->orWhere('listing_location', 'like', '%' . $search . '%');
            });
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('listing_location', 'like', '%' . $request->location . '%');
        }

        // Room type filter
        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        // Rent filter
        if ($request->filled('rent')) {
            switch ($request->rent) {
                case 'below500':
                    $query->where('listing_rent', '<', 500);
                    break;
                case '500-800':
                    $query->whereBetween('listing_rent', [500, 800]);
                    break;
                case '800-1000':
                    $query->whereBetween('listing_rent', [800, 1000]);
                    break;
                case 'above1000':
                    $query->where('listing_rent', '>', 1000);
                    break;
            }
        }

        $sort = $request->input('sort', 'newest');

        match ($sort) {
            'price_asc' => $query->orderBy('listing_rent')->orderByDesc('listing_id'),
            'price_desc' => $query->orderByDesc('listing_rent')->orderByDesc('listing_id'),
            default => $query->latest(),
        };

        $listings = $query->paginate(9)->withQueryString();

        return view('student.listings', compact('listings'));
    }

    public function saved(Request $request)
    {
        $sort = $request->input('sort', 'recent');
        if (! in_array($sort, ['recent', 'price_asc', 'price_desc', 'newest'], true)) {
            $sort = 'recent';
        }

        $query = Auth::user()->savedListings()->visibleTo(Auth::user())
            ->where('listings.listing_status', 'active')
            ->where('listings.listing_availability', 'available')
            ->with('photos');

        match ($sort) {
            'price_asc' => $query->orderBy('listings.listing_rent'),
            'price_desc' => $query->orderByDesc('listings.listing_rent'),
            'newest' => $query->orderByDesc('listings.listing_created_at'),
            default => $query->orderByDesc('listing_saves.save_created_at'),
        };

        $listings = $query->orderByDesc('listings.listing_id')->paginate(9)->withQueryString();
        if ($listings->isEmpty() && $listings->currentPage() > 1) {
            return redirect()->route('student.saved', ['sort' => $sort, 'page' => $listings->lastPage()]);
        }

        return view('student.saved-listings', compact('listings', 'sort'));
    }

    public function toggleSave(Request $request, Listing $listing)
    {
        abort_unless(Listing::visibleTo(Auth::user())->whereKey($listing->getKey())->exists(), 404);
        if ($listing->user_id === Auth::id()) {
            return back()->with('error', 'You cannot save your own listing.');
        }

        if ($listing->listing_availability !== 'available'
            && ! Auth::user()->savedListings()->where('listings.listing_id', $listing->getKey())->exists()) {
            $message = 'This listing is rented or unavailable.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $saved = Auth::user()->savedListings()->toggle($listing->getKey());

        $isSaved = count($saved['attached']) > 0;
        AuditLogger::log(Auth::id(), $isSaved ? 'saved_listing' : 'unsaved_listing', 'Listing ID: '.$listing->getKey());
        $message = $isSaved ? 'Listing saved.' : 'Listing removed from saved listings.';

        if ($request->expectsJson()) {
            return response()->json([
                'saved' => $isSaved,
                'message' => $message,
            ]);
        }

        return back()
            ->with('success', $message);
    }

    public function updateAvailability(Request $request, Listing $listing)
    {
        if (Auth::id() !== $listing->user_id) {
            abort(403, 'You are not allowed to change this listing status.');
        }

        $data = $request->validate([
            'availability' => ['required', 'in:available,rented,unavailable'],
        ]);

        $previousAvailability = $listing->listing_availability;
        $listing->update(['listing_availability' => $data['availability']]);

        AuditLogger::log(
            Auth::id(),
            'updated_listing_availability',
            'Listing ID: ' . $listing->getKey() . ' availability: ' . ucfirst($previousAvailability) . ' to ' . ucfirst($data['availability'])
        );

        return back()->with(
            'success',
            $data['availability'] === 'rented'
                ? 'Listing marked as rented.'
                : ($data['availability'] === 'unavailable'
                    ? 'Listing marked as unavailable.'
                    : 'Listing marked as available.')
        );
    }

    public function bulkRemoveSaved(Request $request)
    {
        $validated = $request->validate([
            'listing_ids' => ['required', 'array', 'min:1'],
            'listing_ids.*' => ['integer', 'exists:listings,listing_id'],
        ]);

        $removedIds = Auth::user()->savedListings()->whereIn('listings.listing_id', $validated['listing_ids'])->pluck('listings.listing_id');
        Auth::user()->savedListings()->detach($validated['listing_ids']);
        foreach ($removedIds as $id) AuditLogger::log(Auth::id(), 'unsaved_listing', 'Listing ID: '.$id);

        return back()
            ->with('success', 'Selected listings removed from your saved listings.');
    }


    // =========================================================
    // SHOW - Show single listing details
    // =========================================================

    public function show(Listing $listing)
    {
        abort_unless(Listing::visibleTo(Auth::user())->whereKey($listing->getKey())->exists(), 404);
        $listing->load('photos');

        AuditLogger::log(
            Auth::id(),
            'viewed_listing',
            'Listing ID: ' . $listing->getKey()
        );

        return view('student.room-details', compact('listing'));
    }


    // =========================================================
    // CREATE - Show create listing form
    // =========================================================

    public function create()
    {
        return view('student.create-listings');
    }

    // =========================================================
    // STORE - Save new listing
    // =========================================================

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:255',
            'rent'        => 'required|numeric|min:0',
            'room_type'   => 'required|string|max:100',
            'pax' => 'required|integer|min:1|max:100',
            'available_from' => 'required|date_format:Y-m-d',
            'rental_period' => ['required', \Illuminate\Validation\Rule::in(array_keys(Listing::RENTAL_PERIODS))],
            'preferred_tenant' => ['required', \Illuminate\Validation\Rule::in(array_keys(Listing::TENANT_PREFERENCES))],
            'facilities' => 'required|array|min:1|max:8',
            'facilities.*' => ['string', 'distinct', \Illuminate\Validation\Rule::in(array_keys(Listing::FACILITIES))],

            'accuracy_confirmed' => 'accepted',
            'photos'      => 'required|array|min:3|max:10',
            'photos.*'    => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
        
        $listing = Listing::create([
            'user_id'      => Auth::id(),
            'listing_title'        => $request->title,
            'listing_description'  => $request->description,
            'listing_location'     => $request->location,
            'listing_rent'         => $request->rent,
            'room_type'    => $request->room_type,
            'pax' => $request->input('pax'),
            'available_from' => $request->input('available_from'),
            'rental_period' => $request->input('rental_period'),
            'preferred_tenant' => $request->input('preferred_tenant') ?? 'any',
            'facilities' => $request->input('facilities') ?? [],
            'listing_photo'        => null,
            'listing_status'       => 'active',
            'listing_availability' => 'available',
            'report_count' => 0,
        ]);


        // -----------------------------------------------------
        // SAVE PHOTOS
        // First photo = main photo
        // All photos saved to listing_photos table
        // -----------------------------------------------------

        if ($request->hasFile('photos')) {

            foreach ($request->file('photos') as $index => $photo) {

                // Store photo in storage/app/public/listings
                $path = $photo->store('listings', 'public');

                // First photo becomes the main photo
                if ($index === 0) {
                    $listing->listing_photo = $path;
                    $listing->save();
                }

                // Save all photos to listing_photos table
                ListingPhoto::create([
                    'listing_id' => $listing->getKey(),
                    'photo_path'      => $path,
                ]);
            }
        }


        // -----------------------------------------------------
        // AUDIT LOG
        // -----------------------------------------------------

        AuditLogger::log(
            Auth::id(),
            'created_listing',
            'Listing ID: ' . $listing->getKey()
        );


        // -----------------------------------------------------
        // REDIRECT
        // -----------------------------------------------------

        return redirect()
            ->route('student.listings.show', $listing->getKey())
            ->with('success', 'Listing posted successfully.');
    }


    // =========================================================
    // EDIT - Show edit listing form
    // =========================================================

    public function edit(Listing $listing)
    {
        // Only owner can edit
        if (Auth::id() !== $listing->user_id) {
            abort(403);
        }

        $listing->load('photos');

        return view('student.listings.edit', compact('listing'));
    }


    // =========================================================
    // UPDATE - Save edited listing
    // =========================================================

    public function update(Request $request, Listing $listing)
    {
        // Only owner can edit
        if (Auth::id() !== $listing->user_id) {
            abort(403, 'You are not allowed to edit this listing.');
        }


        // -----------------------------------------------------
        // VALIDATION
        // -----------------------------------------------------

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:255',
            'rent'        => 'required|numeric|min:0',
            'room_type'   => 'required|string|max:100',
            'pax' => 'required|integer|min:1|max:100',
            'available_from' => 'required|date_format:Y-m-d',
            'rental_period' => ['required', \Illuminate\Validation\Rule::in(array_keys(Listing::RENTAL_PERIODS))],
            'preferred_tenant' => ['required', \Illuminate\Validation\Rule::in(array_keys(Listing::TENANT_PREFERENCES))],
            'facilities' => 'required|array|min:1|max:8',
            'facilities.*' => ['string', 'distinct', \Illuminate\Validation\Rule::in(array_keys(Listing::FACILITIES))],

            'availability' => 'required|in:available,rented,unavailable',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'photos'      => 'nullable|array|max:10',
            'photos.*'    => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'delete_photos'   => 'nullable|array',
            'delete_photos.*' => 'nullable|integer',
        ]);


        // -----------------------------------------------------
        // UPDATE BASIC INFORMATION
        // -----------------------------------------------------

        $listing->listing_title       = $request->title;
        $listing->listing_description = $request->description;
        $listing->listing_location    = $request->location;
        $listing->listing_rent        = $request->rent;
        $listing->room_type   = $request->room_type;
        $listing->pax = $request->input('pax');
        $listing->listing_availability = $request->availability;
        $listing->available_from = $request->input('available_from');
        $listing->rental_period = $request->input('rental_period');
        $listing->preferred_tenant = $request->input('preferred_tenant') ?? 'any';
        $listing->facilities = $request->input('facilities') ?? [];


        // -----------------------------------------------------
        // REPLACE MAIN PHOTO
        // -----------------------------------------------------

        if ($request->hasFile('photo')) {

            // Delete old main photo from storage
            if ($listing->listing_photo && Storage::disk('public')->exists($listing->listing_photo)) {
                Storage::disk('public')->delete($listing->listing_photo);
            }

            // Save new main photo
            $listing->listing_photo = $request->file('photo')->store('listings', 'public');
        }

        $listing->save();


        // -----------------------------------------------------
        // DELETE SELECTED ADDITIONAL PHOTOS
        // -----------------------------------------------------

        if ($request->filled('delete_photos')) {

            foreach ($request->delete_photos as $photoId) {

                $photo = ListingPhoto::find($photoId);

                if ($photo && $photo->listing_id === $listing->getKey()) {

                    // Delete from storage
                    if (Storage::disk('public')->exists($photo->photo_path)) {
                        Storage::disk('public')->delete($photo->photo_path);
                    }

                    // Delete from database
                    $photo->delete();
                }
            }
        }


        // -----------------------------------------------------
        // ADD NEW ADDITIONAL PHOTOS
        // -----------------------------------------------------

        if ($request->hasFile('photos')) {

            foreach ($request->file('photos') as $photo) {

                $path = $photo->store('listings', 'public');

                ListingPhoto::create([
                    'listing_id' => $listing->getKey(),
                    'photo_path'      => $path,
                ]);
            }
        }


        // -----------------------------------------------------
        // AUDIT LOG
        // -----------------------------------------------------

        AuditLogger::log(
            Auth::id(),
            'updated_listing',
            'Listing ID: ' . $listing->getKey()
        );


        // -----------------------------------------------------
        // REDIRECT
        // -----------------------------------------------------

        return redirect()
            ->route('student.listings.show', $listing->getKey())
            ->with('success', 'Listing updated successfully.');
    }


    // =========================================================
    // DESTROY - Delete listing
    // =========================================================

    public function destroy(Listing $listing)
    {
        // Only owner can delete
        if (Auth::id() !== $listing->user_id) {
            abort(403);
        }

        // Delete main photo from storage
        if ($listing->listing_photo && Storage::disk('public')->exists($listing->listing_photo)) {
            Storage::disk('public')->delete($listing->listing_photo);
        }

        // Delete all additional photos from storage
        foreach ($listing->photos as $photo) {
            if (Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
        }

        // Delete listing (photos deleted automatically via cascade)
        $listing->delete();

        AuditLogger::log(
            Auth::id(),
            'deleted_listing',
            'Listing ID: ' . $listing->getKey()
        );

        return redirect()
            ->route('student.listings')
            ->with('success', 'Listing deleted successfully.');
    }
}
