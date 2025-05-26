<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'make:admin {email} {password}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
        ]);

        $user->assignRole('admin');

        $this->info('Admin user created successfully!');
    }
}
