<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="card" style="max-width: 400px; margin: 2rem auto;">
        <h1 style="text-align: center; margin-bottom: 1.5rem;">Login</h1>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="{{ old('email') }}">
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>

        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </p>

        <hr style="margin: 1.5rem 0;">
        <p style="font-size: 0.9rem;"><strong>Demo Credentials:</strong></p>
        <p style="font-size: 0.85rem;">
            Email: sales@example.com<br>
            Password: password
        </p>
    </div>
@endsection