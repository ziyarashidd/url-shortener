<!-- resources/views/invitations/create.blade.php -->
@extends('layouts.app')

@section('title', 'Invite User')

@section('content')
    <div class="card" style="max-width: 500px;">
        <h1>Invite User</h1>

        <form action="{{ route('invitations.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role_id" required>
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn">Send Invitation</button>
        </form>
    </div>
@endsection