<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-password {email? : The email of the user to reset password for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the password for a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if (! $email) {
            $email = $this->ask('Please enter the user\'s email address');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User not found with email: {$email}");
            return Command::FAILURE;
        }

        $this->info("Resetting password for user: {$user->name} ({$user->email})");

        $password = $this->secret('Please enter the new password');
        $passwordConfirmation = $this->secret('Please confirm the new password');

        if ($password !== $passwordConfirmation) {
            $this->error('Passwords do not match.');
            return Command::FAILURE;
        }

        $validator = Validator::make(['password' => $password], [
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        $this->info('Password has been successfully reset.');

        return Command::SUCCESS;
    }
}
