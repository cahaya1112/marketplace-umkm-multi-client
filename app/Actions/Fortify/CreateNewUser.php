<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'nomor_telepon' => ['nullable', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'password' => $this->passwordRules(),
        ], [
            'nomor_telepon.regex' => 'Nomor telepon tidak valid. Contoh: 081234567890.',
        ])->validate();

        return User::create([
            'nama' => $input['name'],
            'email' => $input['email'],
            'nomor_telepon' => $input['nomor_telepon'] ?? null,
            'kata_sandi' => $input['password'],
            'peran' => 'PEMBELI',
        ]);
    }
}