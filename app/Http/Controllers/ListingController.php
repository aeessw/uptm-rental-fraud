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
    // INDEX - Show all active listings
    // =========================================================

    public function index(Request $request)
    {
        $query = Listing::where('status', 'active')->with('photos');

        // Search by title or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Room type filter
        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        // Rent filter
        if ($request->filled('rent')) {
            switch ($request->rent) {
                case 'below500':
                    $query->where('rent', '<', 500);
                    break;
                case '500-800':
                    $query->whereBetween('rent', [500, 800]);
                    break;
                case '800-1000':
                    $query->whereBetween('rent', [800, 1000]);
                    break;
                case 'above1000':
                    $query->where('rent', '>', 1000);
                    break;
            }
        }

        $listings = $query->latest()->paginate(9)->withQueryString();

        return view('student.listings', compact('listings'));
    }


    // =========================================================
    // SHOW - Show single listing details
    // =========================================================

    public function show(Listing $listing)
    {
        $listing->load('photos');

        AuditLogger::log(
            Auth::id(),
            'viewed_listing',
            'Listing ID: ' . $listing->id
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
        // -----------------------------------------------------
        // VALIDATION
        // -----------------------------------------------------

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:255',
            'rent'        => 'required|numeric|min:0',
            'room_type'   => 'required|string|max:100',
            'photos'      => 'required|array|min:1|max:10',
            'photos.*'    => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
        


        // -----------------------------------------------------
        // CREATE LISTING
        // -----------------------------------------------------

        $listing = Listing::create([
            'user_id'      => Auth::id(),
            'title'        => $request->title,
            'description'  => $request->description,
            'location'     => $request->location,
            'rent'         => $request->rent,
            'room_type'    => $request->room_type,
            'photo'        => null,
            'status'       => 'active',
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
                    $listing->photo = $path;
                    $listing->save();
                }

                // Save all photos to listing_photos table
                ListingPhoto::create([
                    'listing_id' => $listing->id,
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
            'Listing ID: ' . $listing->id
        );


        // -----------------------------------------------------
        // REDIRECT
        // -----------------------------------------------------

        return redirect()
            ->route('student.listings.show', $listing->id)
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
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'photos'      => 'nullable|array|max:10',
            'photos.*'    => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'delete_photos'   => 'nullable|array',
            'delete_photos.*' => 'nullable|integer',
        ]);


        // -----------------------------------------------------
        // UPDATE BASIC INFORMATION
        // -----------------------------------------------------

        $listing->title       = $request->title;
        $listing->description = $request->description;
        $listing->location    = $request->location;
        $listing->rent        = $request->rent;
        $listing->room_type   = $request->room_type;


        // -----------------------------------------------------
        // REPLACE MAIN PHOTO
        // -----------------------------------------------------

        if ($request->hasFile('photo')) {

            // Delete old main photo from storage
            if ($listing->photo && Storage::disk('public')->exists($listing->photo)) {
                Storage::disk('public')->delete($listing->photo);
            }

            // Save new main photo
            $listing->photo = $request->file('photo')->store('listings', 'public');
        }

        $listing->save();


        // -----------------------------------------------------
        // DELETE SELECTED ADDITIONAL PHOTOS
        // -----------------------------------------------------

        if ($request->filled('delete_photos')) {

            foreach ($request->delete_photos as $photoId) {

                $photo = ListingPhoto::find($photoId);

                if ($photo && $photo->listing_id === $listing->id) {

                    // Delete from storage
                    if (Storage::disk('public')->exists($photo->photo)) {
                        Storage::disk('public')->delete($photo->photo);
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
                    'listing_id' => $listing->id,
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
            'Listing ID: ' . $listing->id
        );


        // -----------------------------------------------------
        // REDIRECT
        // -----------------------------------------------------

        return redirect()
            ->route('student.listings.show', $listing->id)
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
        if ($listing->photo && Storage::disk('public')->exists($listing->photo)) {
            Storage::disk('public')->delete($listing->photo);
        }

        // Delete all additional photos from storage
        foreach ($listing->photos as $photo) {
            if (Storage::disk('public')->exists($photo->photo)) {
                Storage::disk('public')->delete($photo->photo);
            }
        }

        // Delete listing (photos deleted automatically via cascade)
        $listing->delete();

        AuditLogger::log(
            Auth::id(),
            'deleted_listing',
            'Listing ID: ' . $listing->id
        );

        return redirect()
            ->route('student.listings')
            ->with('success', 'Listing deleted successfully.');
    }
}