<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List the authenticated user's favorite movies.
     */
    public function index()
    {
        $favorites = auth()->user()->favorites()->latest()->get();

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Add a movie to the authenticated user's favorites.
     * Called via AJAX from both the list and detail pages.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'imdb_id' => 'required|string',
            'title' => 'required|string',
            'year' => 'nullable|string',
            'type' => 'nullable|string',
            'poster' => 'nullable|string',
        ]);

        $favorite = auth()->user()->favorites()->firstOrCreate(
            ['imdb_id' => $validated['imdb_id']],
            [
                'title' => $validated['title'],
                'year' => $validated['year'] ?? null,
                'type' => $validated['type'] ?? null,
                'poster' => $validated['poster'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'favorite' => $favorite,
        ]);
    }

    /**
     * Remove a movie from the authenticated user's favorites.
     */
    public function destroy(string $imdbId)
    {
        auth()->user()->favorites()->where('imdb_id', $imdbId)->delete();

        return response()->json(['success' => true]);
    }
}
