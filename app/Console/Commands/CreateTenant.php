<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTenant extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'create:tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //get inputs from the user to setup tenant
        $name = $this->ask('Enter the name for the tenant:');
        $domain = $this->ask('Enter the domain for the tenant:');
        $username = $this->ask('Enter the username for the tenant:');
        $password = $this->secret('Enter the password for the tenant:');
        // Create database name
        $dbName = 'tenant_' . $username;
        // Create user
        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $username . '@gmail.com',
            'password' => bcrypt($password),
        ]);
        // Store tenant information in the main database
        Tenant::create([
            'name' => $name,
            'user_id' => $user->id,
            'domain' => $domain,
            'database_name' => $dbName,
            'database_username' => $username,
            'database_password' => $password,
        ]);
        // Create database
        DB::statement("CREATE DATABASE $dbName");
        $this->info("Tenant {$domain} created successfully.");
    }
}
