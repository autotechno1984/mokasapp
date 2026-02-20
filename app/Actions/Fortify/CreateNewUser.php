<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'nama_tenant' => ['required', 'string', 'max:255', Rule::unique(Tenant::class, 'nama_tenant')],
            'subdomain' => ['required', 'string', 'min:3', 'max:63', 'alpha_dash', Rule::unique(Tenant::class, 'subdomain')],
            'jenis_usaha' => ['required', 'string', 'max:200'],
            'password' => $this->passwordRules(),
        ])->validate();

        $trialEndsAt = now()->addDays(7)->toDateString();

        $user = DB::transaction(function () use ($input, $trialEndsAt): User {
            $tenant = Tenant::create([
                'nama_tenant' => $input['nama_tenant'],
                'subdomain' => strtolower($input['subdomain']),
                'jenis_usaha' => $input['jenis_usaha'],
                'status' => 'trial',
                'data' => [
                    'trial_started_at' => now()->toDateString(),
                    'trial_ends_at' => $trialEndsAt,
                ],
            ]);

            return User::create([
                'tenant_id' => $tenant->id,
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);
        });

        session()->flash('trial_toast', [
            'type' => 'info',
            'title' => 'Trial 7 hari aktif',
            'description' => "Akun tenant Anda aktif hingga {$trialEndsAt}.",
            'trial_ends_at' => $trialEndsAt,
        ]);

        return $user;
    }
}
