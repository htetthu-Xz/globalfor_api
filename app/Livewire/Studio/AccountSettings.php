<?php

namespace App\Livewire\Studio;

use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AccountSettings extends Component
{
    public string $name = '';

    public string $email = '';

    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    public function updateProfile(): void
    {
        $user = auth()->user();
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
        ]);

        $this->dispatch('profile-saved');
        session()->flash('profile-status', 'Profile updated.');
    }

    public function updatePassword(UpdateUserPassword $action): void
    {
        $action->update(auth()->user(), [
            'current_password' => $this->currentPassword,
            'password' => $this->password,
            'password_confirmation' => $this->passwordConfirmation,
        ]);

        $this->reset('currentPassword', 'password', 'passwordConfirmation');
        session()->flash('password-status', 'Password updated.');
    }

    public function render(): View
    {
        return view('livewire.studio.account-settings');
    }
}
