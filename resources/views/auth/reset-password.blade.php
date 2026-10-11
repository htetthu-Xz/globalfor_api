@extends('layouts.auth', ['title' => 'Choose a new password'])

@section('content')
    <div class="studio-auth-heading">
        <p class="studio-kicker"><span></span> SECURE YOUR ACCOUNT</p>
        <h2>Choose a new password</h2>
        <p>Use at least 12 characters with uppercase, lowercase, a number and a symbol.</p>
    </div>

    @if ($errors->any())
        <div class="studio-alert studio-alert--error" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="studio-form">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <label class="studio-field">
            <span>Email address</span>
            <input type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="email">
        </label>
        <label class="studio-field">
            <span>New password</span>
            <input type="password" name="password" required autocomplete="new-password">
        </label>
        <label class="studio-field">
            <span>Confirm new password</span>
            <input type="password" name="password_confirmation" required autocomplete="new-password">
        </label>
        <button type="submit" class="studio-button studio-button--primary studio-button--full">Save new password <span aria-hidden="true">↗</span></button>
    </form>
@endsection
