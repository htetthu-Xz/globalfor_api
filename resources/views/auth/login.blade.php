@extends('layouts.auth', ['title' => 'Sign in'])

@section('content')
    <div class="studio-auth-heading">
        <p class="studio-kicker"><span></span> WELCOME BACK</p>
        <h2>Sign in to Studio</h2>
        <p>Use your administrator account to continue.</p>
    </div>

    @if (session('status'))
        <div class="studio-alert studio-alert--success" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="studio-alert studio-alert--error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="studio-form">
        @csrf
        <label class="studio-field">
            <span>Email address</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@globalformm.com">
        </label>
        <label class="studio-field">
            <span>Password</span>
            <input type="password" name="password" required autocomplete="current-password" placeholder="Your password">
        </label>
        <div class="studio-form-row">
            <label class="studio-check"><input type="checkbox" name="remember"><span>Keep me signed in</span></label>
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>
        <button type="submit" class="studio-button studio-button--primary studio-button--full">
            Enter Studio <span aria-hidden="true">↗</span>
        </button>
    </form>
@endsection
