<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FaqEntry;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ucc.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Helpdesk agent
        User::create([
            'name' => 'Agent Smith',
            'email' => 'agent@ucc.local',
            'password' => Hash::make('password123'),
            'role' => 'helpdesk_agent',
        ]);

        // Regular user
        User::create([
            'name' => 'John Doe',
            'email' => 'user@ucc.local',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // FAQ entries
        FaqEntry::create([
            'keywords' => 'nyitvatartás, nyitva, zárva, mikor',
            'answer' => 'Ügyfélszolgálatunk hétköznap 9-17 óráig áll rendelkezésére.',
        ]);

        FaqEntry::create([
            'keywords' => 'email, elérhetőség, cím',
            'answer' => 'Elérhetőségeink: support@ucc.local, +36 1 234 5678',
        ]);

        FaqEntry::create([
            'keywords' => 'event, esemény, hogyan',
            'answer' => 'Az Events menüben tudsz új eseményt létrehozni. Add meg a címet és az időpontot!',
        ]);

        FaqEntry::create([
            'keywords' => 'jelszó, password, reset',
            'answer' => 'Jelszó visszaállításhoz használd a "Forgot Password" funkciót a bejelentkezési oldalon.',
        ]);
    }
}