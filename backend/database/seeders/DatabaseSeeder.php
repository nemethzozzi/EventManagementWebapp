<?php

namespace Database\Seeders;

use App\Models\FaqEntry;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
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
            'role' => Message::SENDER_USER,
        ]);

        // FAQ entries
        FaqEntry::create([
            'keywords' => 'esemény létrehozás, event létrehozás, új esemény, hogyan hozzak létre',
            'answer' => 'Esemény létrehozásához add meg a címet, válassz dátumot, majd írd be a leírást és mentsd el.',
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
            'keywords' => 'leírás szerkesztés, esemény szerkesztés, event szerkesztés, módosítás',
            'answer' => 'Esemény szerkesztéséhez nyisd meg az eseményt, módosítsd a leírást vagy a címet, majd mentsd el.',
        ]);
        FaqEntry::create([
            'keywords' => 'jelszó, password, reset',
            'answer' => 'Jelszó visszaállításhoz használd a "Forgot Password" funkciót a bejelentkezési oldalon.',
        ]);
        FaqEntry::create([
            'keywords' => 'dátum módosítás, időpont módosítás, dátum választás, mikor lesz',
            'answer' => 'A dátumot az esemény szerkesztésénél tudod módosítani: válassz új időpontot, majd mentsd el.',
        ]);
        FaqEntry::create([
            'keywords' => 'törlés, esemény törlés, event törlés, hogyan töröljem',
            'answer' => 'Egy esemény törléséhez nyisd meg az eseményt és válaszd a Törlés opciót. A törlés végleges lehet (rendszertől függően).',
        ]);
        FaqEntry::create([
            'keywords' => 'nem találom, hol van, eltűnt, lista',
            'answer' => 'Ellenőrizd az események listáját és a szűrőket. Ha dátum szerint szűrsz, lehet, hogy az esemény a másik időszakban van.',
        ]);
        FaqEntry::create([
            'keywords' => 'agent, ügyintéző, ember, support, segítség',
            'answer' => 'Rendben, kapcsolok egy ügyintézőt.',
        ]);
    }
}
