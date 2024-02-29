<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $user = User::factory()->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
         ]);

        $token = $user->createToken('test-token');

        $this->command->info("You can use this token to make requests: $token->plainTextToken");
    }
}
