<!-- resources/views/short-urls/create.blade.php -->
@extends('layouts.app')

@section('title', 'Create Short URL')

@section('content')
    <div class="card" style="max-width: 600px;">
        <h1>Create Short URL</h1>

        <form action="{{ route('short-urls.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Original URL</label>
                <input type="url" name="original_url" placeholder="https://example.com" required>
                @error('original_url')<div class="error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn">Generate Short URL</button>
        </form>
    </div>
@endsection