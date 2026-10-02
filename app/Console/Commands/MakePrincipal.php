<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakePrincipal extends Command
{
    protected $signature = 'school:make-principal {email : Email of an existing account}';

    protected $description = 'Assign the Principal role to an existing unlinked account';

    public function handle(): int
    {
        $user = User::query()->where('email', trim($this->argument('email')))->first();

        if (! $user) {
            $this->error('No account was found with that email. Register it first.');

            return self::FAILURE;
        }

        if ($user->studentProfile()->exists() || $user->teacherProfile()->exists()) {
            $this->error('A linked student or teacher account cannot be promoted to Principal.');

            return self::FAILURE;
        }

        $user->forceFill(['role' => User::ROLE_PRINCIPAL])->save();

        $this->info("{$user->email} now has the Principal role.");

        return self::SUCCESS;
    }
}
