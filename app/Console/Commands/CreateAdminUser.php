<?php

namespace App\Console\Commands;

use App\Enums\GenderEnum;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user 
                            {--name= : Full name (first and last name)}
                            {--email= : Email address}
                            {--password= : Password}
                            {--gender= : Gender as a single letter (m/f)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with is_admin=true, reset_password_required=false, is_active=true';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Enter full name');
        $email = $this->option('email') ?? $this->ask('Enter email address');
        $password = $this->option('password') ?? $this->secret('Enter password');
        $genderInput = $this->option('gender') ?? $this->ask('Enter gender (m/f)', 'm');
        $genderInput = strtolower(trim((string) $genderInput));

        $gender = match ($genderInput) {
            'm' => GenderEnum::Male,
            'f' => GenderEnum::Female,
            default => null,
        };

        // Split name into first and last name
        $nameParts = explode(' ', trim($name), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        if ($gender === null) {
            $this->error("Invalid gender value '{$genderInput}'. Allowed values: m, f");

            return Command::FAILURE;
        }

        // Validate input
        $validator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
        ], [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }

            return Command::FAILURE;
        }

        // Create admin user
        try {
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password,
                'gender' => $gender,
                'is_admin' => true,
                'reset_password_required' => false,
                'is_active' => true,
            ]);

            $this->info('Admin user created successfully!');
            $this->info("ID: {$user->id}");
            $this->info("Name: {$user->full_name}");
            $this->info("Email: {$user->email}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create admin user: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }
}
