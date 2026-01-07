<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(Rental $rental)
    {
        if ($rental->user_id !== Auth::id()) {
            abort(403, 'Brak dostępu.');
        }

        if ($rental->status->name !== 'completed') {
            return redirect()->route('rentals.index')->withErrors(['error' => 'Możesz ocenić tylko zakończone wypożyczenia.']);
        }

        if ($rental->review()->exists()) {
            return redirect()->route('rentals.index')->with('info', 'Już wystawiłeś opinię do tego wynajęcia.');
        }

        return view('reviews.create', compact('rental'));
    }

    public function store(Request $request, Rental $rental)
    {
        if ($rental->user_id !== Auth::id()) abort(403);
        
        if ($rental->review()->exists()) {
            return redirect()->route('rentals.index')->with('error', 'Opinia do tego wynajęcia już istnieje.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'car_id' => $rental->car_id,
            'rental_id' => $rental->id, 
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('rentals.index')->with('success', 'Dziękujemy za Twoją opinię!');
    }

    public function destroy(Review $review)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $review->delete();
        return back()->with('success', 'Opinia została usunięta.');
    }
}