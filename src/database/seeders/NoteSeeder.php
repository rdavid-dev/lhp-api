<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        Note::truncate();

        //Create user
        User::factory()->count(20)->create();

        //loop users to create notes for each one of them
        foreach(User::all() as $user) {
            Note::factory()->count(rand(2,5))->create([
                'user_id' => $user->id
            ]);
        }
    }
}
