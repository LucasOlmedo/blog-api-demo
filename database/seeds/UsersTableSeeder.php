<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        factory(\Modules\User\Entities\User::class, 5)->create()->each(function ($user) use ($faker) {
            $user->name = $faker->name;
            $user->email = $faker->email;
            $user->avatar = 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($faker->email))).'?d=identicon&s=200&f=y';
            $this->command->info("User {$user->name} was created!");
        });
    }
}
