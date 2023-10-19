<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Console\Command;

class MigrateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate a tenant database based on their username';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->ask('Enter the username of the tenant to migrate:');
        $this->info("Entered username: $username"); // Add this line for debugging

        $user = User::where('name', $username)->first();
        $this->info("Found user: " . json_encode($user));

        if (!$user) {
            $this->error("User with the username '{$username}' not found.");
            return;
        }

        $tenant = Tenant::where('user_id', $user->id)->first();
        $this->info("Found tenant: " . json_encode($tenant));

        if (!$tenant) {
            $this->error("Tenant {$username} not found.");
            return;
        }

        config(['database.connections.tenant.database' => $tenant->database_name]);
        // config(['database.connections.tenant.username' => $tenant->database_username]);
        // config(['database.connections.tenant.password' => $tenant->database_password]);
       $this->call('migrate', ['--database' => 'tenant', '--path' => 'database/migrations/tenant']);
        $this->info("Migrations run for tenant {$username}.");
    }
}
