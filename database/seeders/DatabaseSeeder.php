<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {



        $user =User::factory()->create([
            'name'=> 'John Doe'

        ]);

         Post::factory()->create([
            'user_id' => $user->id
         ]);

        // $personal = Category:: create([

        //     'name' => 'Personal',
        //     'slug'=> 'personal'
        // ]);

        // $family= Category:: create([

        //     'name' => 'Family',
        //     'slug'=> 'family'
        // ]);

        // $work= Category:: create([

        //     'name' => 'Work',
        //     'slug'=> 'work'
        // ]);

        // Post::create([
        //     'user_id' => $user->id,
        //     'category_id' => $family->id,
        //     'title' => 'My Family Post',
        //     'slug'=> 'my-family-post',
        //     'excerpt' => '<p> Excerpt for my post </p> ',
        //     'body'=> '<p> Lorem ipsum dolor sit amet consectetur adipisicing elit.Iste provident doloribus est       officiis reiciendis magni a perferendis ratione dolorum ipsa animi, culpa ullam amet dignissimos vero commodi autem moles
        //     tias suscipit.</p>'
        // ]);

        // Post::create([
        //     'user_id' => $user->id,
        //     'category_id' => $work->id,
        //     'title' => 'My Work Post',
        //     'slug'=> 'my-work-post',
        //     'excerpt' => '<p> Excerpt for my post</p> ',
        //     'body'=> '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit.Iste provident doloribus est       officiis reiciendis magni a perferendis ratione dolorum ipsa animi, culpa ullam amet dignissimos vero commodi autem moles
        //     tias suscipit. </p>'
        // ]);
    }



}
