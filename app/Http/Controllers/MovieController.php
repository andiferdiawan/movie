<?php

namespace App\Http\Controllers;

use App\Services\OmdbService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /** @var OmdbService */
    protected $omdb;

    public function __construct(OmdbService $omdb)
    {
        $this->middleware('auth');
        $this->omdb = $omdb;
    }

    /**
     * Movie list / search page. Results themselves are loaded via AJAX
     * from search() so the page can implement infinite scroll.
     */
    public function index(Request $request)
    {
        $filters = $request->only('q', 'type', 'year');

        // OMDb has no "list all movies" endpoint, so seed the very first
        // load with a default keyword instead of an empty results grid.
        if (empty($filters['q'])) {
            $filters['q'] = config('services.omdb.default_query');
        }

        return view('movies.index', compact('filters'));
    }

    /**
     * AJAX endpoint used for both the initial search and infinite scroll
     * pagination. Returns JSON with the current page of results plus
     * whether more pages are available.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
            'type' => 'nullable|in:movie,series,episode',
            'year' => 'nullable|digits:4',
            'page' => 'nullable|integer|min:1|max:100',
        ]);

        $keyword = trim((string) $request->input('q', ''));
        $page = (int) $request->input('page', 1);

        if ($keyword === '') {
            return response()->json([
                'results' => [],
                'total' => 0,
                'page' => $page,
                'has_more' => false,
                'error' => null,
            ]);
        }

        $data = $this->omdb->search(
            $keyword,
            $page,
            $request->input('type'),
            $request->input('year')
        );

        $results = ($data['Response'] ?? 'False') === 'True' ? $data['Search'] : [];
        $total = (int) ($data['totalResults'] ?? 0);

        $favoriteIds = auth()->user()->favorites()->pluck('imdb_id')->all();

        foreach ($results as &$movie) {
            $movie['is_favorite'] = in_array($movie['imdbID'], $favoriteIds, true);
        }
        unset($movie);

        return response()->json([
            'results' => $results,
            'total' => $total,
            'page' => $page,
            'has_more' => ($page * 10) < $total,
            'error' => ($data['Response'] ?? 'False') === 'False' ? ($data['Error'] ?? null) : null,
        ]);
    }

    /**
     * Movie detail page.
     */
    public function show(string $imdbId)
    {
        $movie = $this->omdb->find($imdbId);

        if (($movie['Response'] ?? 'False') === 'False') {
            abort(404, $movie['Error'] ?? 'Movie not found.');
        }

        $isFavorite = auth()->user()->favorites()->where('imdb_id', $imdbId)->exists();

        return view('movies.show', compact('movie', 'isFavorite'));
    }
}
