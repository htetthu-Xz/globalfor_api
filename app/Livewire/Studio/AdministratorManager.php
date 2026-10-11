<?php

namespace App\Livewire\Studio;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class AdministratorManager extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public bool $showCreate = false;

    public function createAdministrator(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(12)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ]);

        User::query()->create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $this->reset('name', 'email', 'password', 'passwordConfirmation', 'showCreate');
        session()->flash('admin-status', 'Administrator created.');
    }

    public function toggleActive(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        if ($user->is(auth()->user())) {
            $this->addError('administrator', 'You cannot deactivate the account you are currently using.');

            return;
        }

        if ($user->is_active && User::query()->where('is_active', true)->count() <= 1) {
            $this->addError('administrator', 'At least one active administrator is required.');

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        session()->flash('admin-status', $user->is_active ? 'Administrator reactivated.' : 'Administrator deactivated.');
    }

    public function render(): View
    {
        return view('livewire.studio.administrator-manager', [
            'administrators' => User::query()->orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }
}
