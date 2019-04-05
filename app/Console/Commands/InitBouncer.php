<?php

namespace App\Console\Commands;

use Bouncer;
use App\User;
use Illuminate\Console\Command;

class InitBouncer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:bouncer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init default rbac roles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Define roles
        $admin = Bouncer::role()->create([
            'name' => 'admin',
            'title' => 'Administrator'
        ]);

        $member = Bouncer::role()->create([
            'name' => 'member',
            'title' => 'Member'
        ]);

        $guest = Bouncer::role()->create([
            'name' => 'guest',
            'title' => 'Guest'
        ]);

        // Define rights
        $manageMembers = Bouncer::ability()->create([
            'name' => 'manage-members',
            'title' => 'Manage Members'
        ]);

        // manage songs, albums, artists
        $manageAll = Bouncer::ability()->create([
            'name' => 'manage-all',
            'title' => 'Manage All'
        ]);

        // view songs, albums, artists
        $viewAll = Bouncer::ability()->create([
            'name' => 'view-all',
            'title' => 'View All'
        ]);

        // Assign rights to roles
        Bouncer::allow($admin)->to($manageMembers);

        Bouncer::allow($member)->to($manageAll);
        Bouncer::allow($member)->to($viewAll);

        Bouncer::allow($guest)->to($viewAll);

        // Assign roles to users
        $user = User::where('email', 'admin@mymusic.info')->first();
        Bouncer::assign($admin)->to($user);

        $user = User::where('email', 'user1@mymusic.info')->first();
        Bouncer::assign($member)->to($user);

        $user = User::where('email', 'user2@mymusic.info')->first();
        Bouncer::assign($guest)->to($user);
    }
}
