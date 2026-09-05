<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuditLogger;

class ReportController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // Prevent the same student from reporting
        // the same listing more than once.
       // $alreadyReported = Report::where('listing_id', $listing->id)
           // ->where('user_id', Auth::id())
           // ->exists();

        //if ($alreadyReported) {
        //    return back()->with(
               // 'error',
              //  'You have already reported this listing.'
          //  );
       // }//

        // Create report
        Report::create([
            'listing_id' => $listing->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
        ]);

        // Increase report count
        $listing->increment('report_count');

        // Record report action in audit log
        AuditLogger::log(
            Auth::id(),
            'reported_listing',
            'Listing ID: ' . $listing->id
        );

        // Auto-hide listing after 3 reports
        if ($listing->fresh()->report_count >= 3) {
            $listing->update([
                'status' => 'hidden',
            ]);

            // Record automatic hiding in audit log
            AuditLogger::log(
                Auth::id(),
                'listing_auto_hidden',
                'Listing ID: ' . $listing->id
            );
        }

        return back()->with(
            'success',
            'Listing reported successfully.'
        );


    }
}