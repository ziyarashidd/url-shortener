<?php

// app/Http/Controllers/ShortUrlController.php
namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot view URLs');
        }

        $query = ShortUrl::forCompany($companyId);

        if ($user->isAdmin()) {
            // Admin sees all company URLs except own
            $query = $query->exceptUser($user->id);
        } elseif ($user->isMember()) {
            // Member sees all company URLs except own
            $query = $query->exceptUser($user->id);
        } else {
            // Sales/Manager see their own URLs
            $query = $query->where('user_id', $user->id);
        }

        $urls = $query->with('user')->paginate(10);

        return view('short-urls.index', compact('urls'));
    }

    public function create()
    {
        if (!Auth::user()->canCreateUrl()) {
            abort(403, 'You cannot create short URLs.');
        }

        return view('short-urls.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canCreateUrl()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
        ]);

        $user = Auth::user();

        $shortUrl = ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'short_code' => ShortUrl::generateShortCode(),
            'original_url' => $validated['original_url'],
            'clicks' => 0,
        ]);

        if ($request->expectsJson()) {
            return response()->json($shortUrl, 201);
        }

        return redirect()->route('short-urls.show', $shortUrl->id)
                       ->with('success', 'Short URL created successfully!');
    }

    public function show(ShortUrl $shortUrl)
    {
        $user = Auth::user();

        // User can view their own URLs
        if ($shortUrl->user_id !== $user->id) {
            // Admin can view company URLs
            if (!$user->isAdmin() || $shortUrl->company_id !== $user->company_id) {
                abort(403);
            }
        }

        return view('short-urls.show', compact('shortUrl'));
    }

    public function destroy(ShortUrl $shortUrl)
    {
        $user = Auth::user();

        if ($shortUrl->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $shortUrl->delete();

        return redirect()->back()->with('success', 'Short URL deleted.');
    }

    public function redirect($shortCode)
    {
        // Require authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access short URLs.');
        }

        $shortUrl = ShortUrl::where('short_code', $shortCode)->firstOrFail();

        // Track the click
        $shortUrl->incrementClicks();

        return redirect()->away($shortUrl->original_url);
    }
}