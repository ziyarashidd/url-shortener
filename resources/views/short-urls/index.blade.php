<!-- resources/views/short-urls/index.blade.php -->
@extends('layouts.app')

@section('title', 'Short URLs')

@section('content')
    <h1>Short URLs</h1>

    @if($urls->count())
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Short Code</th>
                        <th>Original URL</th>
                        <th>Created By</th>
                        <th>Clicks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($urls as $url)
                        <tr>
                            <td><code>{{ $url->short_code }}</code></td>
                            <td>{{ substr($url->original_url, 0, 50) }}...</td>
                            <td>{{ $url->user->name }}</td>
                            <td>{{ $url->clicks }}</td>
                            <td>
                                <a href="{{ route('short-urls.show', $url) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $urls->links() }}
    @else
        <div class="card">
            <p>No URLs found.</p>
        </div>
    @endif
@endsection