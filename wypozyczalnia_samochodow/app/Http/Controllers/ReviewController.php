<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Rental; // Import Rental
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Formularz dodawania opinii dla konkretnej rezerwacji.
     */
    public function create(Rental $rental)
    {
        // 1. Sprawdź, czy rezerwacja należy do zalogowanego użytkownika
        if ($rental->user_id !== Auth::id()) {
            abort(403, 'Brak dostępu do tej rezerwacji.');
        }

        // 2. Sprawdź, czy rezerwacja jest zakończona
        if ($rental->status->name !== 'completed') {
            return redirect()->route('rentals.index')->withErrors(['error' => 'Możesz ocenić tylko zakończone wypożyczenia.']);
        }

        return view('reviews.create', compact('rental'));
    }

    /**
     * Zapis opinii.
     */
    public function store(Request $request, Rental $rental)
    {
        if ($rental->user_id !== Auth::id()) abort(403);
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'car_id' => $rental->car_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('rentals.index')->with('success', 'Dziękujemy za Twoją opinię!');
    }

    /**
     * Usuwa opinię (Tylko dla Admina).
     */
    public function destroy(Review $review)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Brak uprawnień do usunięcia opinii.');
        }

        $review->delete();

        return back()->with('success', 'Opinia została usunięta.');
    }
}