@extends('layouts.auth', ['title' => 'Confirm password'])

@section('content')
    <div class="studio-auth-heading">
        <p class="studio-kicker"><span></span> SECURITY CHECK</p>
        <h2>Confirm your password</h2>
        <p>Please confirm your password before continuing.</p>
    </div>
    @if ($errors->any())
        <div class="studio-alert studio-alert--error" role="alert">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('password.confirm') }}" class="studio-form">
        @csrf
        <label class="studio-field">
            <span>Password</span>
            <input type="password" name="password" required autocomplete="current-password" autofocus>
        </label>
        <button type="submit" class="studio-button studio-button--primary studio-button--full">Confirm and continue</button>
    </form>
@endsection
