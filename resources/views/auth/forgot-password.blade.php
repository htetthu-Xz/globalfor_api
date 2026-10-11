@extends('layouts.auth', ['title' => 'Reset password'])

@section('content')
    <div class="studio-auth-heading">
        <p class="studio-kicker"><span></span> ACCOUNT RECOVERY</p>
        <h2>Reset your password</h2>
        <p>Enter your administrator email and we’ll send a secure reset link.</p>
    </div>

    @if (session('status'))
        <div class="studio-alert studio-alert--success" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="studio-alert studio-alert--error" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="studio-form">
        @csrf
        <label class="studio-field">
            <span>Email address</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="name@globalformm.com">
        </label>
        <button type="submit" class="studio-button studio-button--primary studio-button--full">Send reset link <span aria-hidden="true">↗</span></button>
        <a class="studio-back-link" href="{{ route('login') }}">← Back to sign in</a>
    </form>
@endsection
