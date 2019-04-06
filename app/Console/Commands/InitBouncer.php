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

        // Manage & View
        $manageUsers = Bouncer::ability()->create([
            'name' => 'manage-users',
            'title' => 'Manage Users'
        ]);

        $manageSongs = Bouncer::ability()->create([
            'name' => 'manage-songs',
            'title' => 'Manage Songs'
        ]);

        $manageArtists = Bouncer::ability()->create([
            'name' => 'manage-artists',
            'title' => 'Manage Artists'
        ]);

        $manageAlbums = Bouncer::ability()->create([
            'name' => 'manage-albums',
            'title' => 'Manage Albums'
        ]);

        // view songs, albums, artists (exlude users)
        $viewAll = Bouncer::ability()->create([
            'name' => 'view-all',
            'title' => 'View All'
        ]);

        // Assign rights to roles
        Bouncer::allow($admin)->to($manageUsers);
        Bouncer::allow($admin)->to($manageSongs);
        Bouncer::allow($admin)->to($manageArtists);
        Bouncer::allow($admin)->to($viewAll);

        Bouncer::allow($member)->to($manageAlbums);
        Bouncer::allow($member)->to($viewAll);

        Bouncer::allow($guest)->to($viewAll);

        // Assign roles to users
        $user = User::where('email', 'admin@mymusic.info')->first();
        Bouncer::assign($admin)->to($user);

        $user = User::where('email', 'user1@mymusic.info')->first();
        Bouncer::assign($member)->to($user);

        $user = User::where('email', 'user2@mymusic.info')->first();
        Bouncer::assign($guest)->to($user);

        $user = User::where('email', 'user3@mymusic.info')->first();
        Bouncer::assign($member)->to($user);
    }
}
