<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Welcome, {{ auth()->user()->name }}!</h1>
    <p>Role: <strong>{{ auth()->user()->role->name }}</strong></p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
        <div class="card">
            <h3>Total URLs</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #007bff;">{{ $stats['total_urls'] }}</p>
        </div>
        <div class="card">
            <h3>Total Clicks</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #28a745;">{{ $stats['total_clicks'] }}</p>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="card">
                <h3>Team Members</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #ffc107;">{{ $stats['team_members'] }}</p>
            </div>
            <div class="card">
                <h3>Team URLs</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #17a2b8;">{{ $stats['team_urls'] }}</p>
            </div>
        @endif
    </div>

    @if(auth()->user()->canCreateUrl())
        <div class="card">
            <h2>Create Short URL</h2>
            <form action="{{ route('short-urls.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Original URL</label>
                    <input type="url" name="original_url" placeholder="https://example.com" required>
                </div>
                <button type="submit" class="btn">Generate Short URL</button>
            </form>
        </div>
    @endif

    @if($stats['recent_urls']->count() > 0)
        <div class="card">
            <h2>Recent URLs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Short Code</th>
                        <th>Original URL</th>
                        <th>Clicks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['recent_urls'] as $url)
                        <tr>
                            <td><code>{{ $url->short_code }}</code></td>
                            <td>{{ substr($url->original_url, 0, 50) }}...</td>
                            <td>{{ $url->clicks }}</td>
                            <td>
                                <a href="{{ route('short-urls.show', $url) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection