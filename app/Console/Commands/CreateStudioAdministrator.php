<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateStudioAdministrator extends Command
{
    protected $signature = 'studio:admin';

    protected $description = 'Create or reactivate a Global F.O.R Studio administrator';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Administrator name'));
        $email = strtolower(trim((string) $this->ask('Email address')));
        $password = (string) $this->secret('Password (12+ characters, mixed case, number and symbol)');
        $confirmation = (string) $this->secret('Confirm password');

        $validator = Validator::make(compact('name', 'email', 'password', 'confirmation'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', Password::min(9)->numbers()],
            'confirmation' => ['same:password'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password), 'is_active' => true],
        );

        $this->info('Studio administrator is ready.');

        return self::SUCCESS;
    }
}
