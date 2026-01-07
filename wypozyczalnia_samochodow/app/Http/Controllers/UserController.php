<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Lista użytkowników.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Prosta wyszukiwarka
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sortowanie po roli, potem po nazwie
        $users = $query->orderBy('role')->orderBy('id')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Formularz edycji użytkownika.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Aktualizacja danych i roli.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:admin,employee,client'],
        ]);

        // Zabezpieczenie: Admin nie może odebrać sobie uprawnień admina (jeśli jest jedynym lub edytuje siebie)
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Nie możesz zmienić roli własnego konta administracyjnego.']);
        }

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.users.index')->with('success', "Zaktualizowano użytkownika {$user->name}.");
    }

    /**
     * Usuwanie użytkownika.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'Nie możesz usunąć własnego konta.']);
        }
        $activeStatuses = \App\Models\RentalStatus::whereIn('name', ['pending', 'confirmed', 'ongoing'])->pluck('id');
        
        if ($user->rentals()->whereIn('rental_status_id', $activeStatuses)->exists()) {
             return back()->withErrors(['user' => 'Nie można usunąć użytkownika z aktywnymi rezerwacjami.']);
        }
        $user->rentals()->delete(); 
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty.');
    }
}