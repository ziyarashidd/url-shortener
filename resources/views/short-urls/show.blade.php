<!-- resources/views/short-urls/show.blade.php -->
@extends('layouts.app')

@section('title', 'Short URL Details')

@section('content')
    <div class="card" style="max-width: 600px;">
        <h1>Short URL Details</h1>

        <div style="margin: 2rem 0;">
            <h3>Short Code</h3>
            <p><code style="font-size: 1.2rem;">{{ $shortUrl->short_code }}</code></p>

            <h3 style="margin-top: 1.5rem;">Short URL</h3>
            <p><a href="{{ route('short-url.redirect', $shortUrl->short_code) }}" target="_blank">
                {{ route('short-url.redirect', $shortUrl->short_code) }}
            </a></p>

            <h3 style="margin-top: 1.5rem;">Original URL</h3>
            <p><a href="{{ $shortUrl->original_url }}" target="_blank">{{ $shortUrl->original_url }}</a></p>

            <h3 style="margin-top: 1.5rem;">Statistics</h3>
            <p>Clicks: <strong>{{ $shortUrl->clicks }}</strong></p>
            <p>Created: <strong>{{ $shortUrl->created_at->format('M d, Y') }}</strong></p>
        </div>

        @if($shortUrl->user_id === auth()->id() || auth()->user()->isAdmin())
            <form action="{{ route('short-urls.destroy', $shortUrl) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        @endif
    </div>
@endsection