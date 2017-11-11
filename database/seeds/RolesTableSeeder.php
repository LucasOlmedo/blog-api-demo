<?php

use Illuminate\Database\Seeder;
use Modules\Role\Entities\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = new Role();
        $role_admin->name = 'administrator';
        $role_admin->description = 'The admin user has full access to the system and can register, change, or delete any data.';
        $role_admin->save();

        $role_author = new Role();
        $role_author->name = 'author';
        $role_author->description = 'The author user has limited access to posts, categories, and tags (has control only on posts linked to their ID).';
        $role_author->save();

        $role_sub = new Role();
        $role_sub->name = 'subscriber';
        $role_sub->description = 'The subscriber user does not have access to the dashboard, and can only view and comment posts.';
        $role_sub->save();
    }
}
