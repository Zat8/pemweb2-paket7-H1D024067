<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User Seeder
        \App\Models\User::create(['name'=>'Admin Event','email'=>'admin@unsoed.ac.id','password'=>bcrypt('password'),'role'=>'admin']);
        \App\Models\User::create(['name'=>'Panitia HMJ','email'=>'panitia@unsoed.ac.id','password'=>bcrypt('password'),'role'=>'panitia']);
        \App\Models\User::create(['name'=>'Peserta Uji','email'=>'peserta@unsoed.ac.id','password'=>bcrypt('password'),'role'=>'peserta','institution'=>'Informatika Unsoed']);

        // 2. Kategori & Event Dummy
        $cat = \App\Models\EventCategory::create(['name'=>'Seminar Teknologi']);
        \App\Models\Event::create([
            'event_category_id' => $cat->id,
            'created_by' => 1,
            'title' => 'Workshop Laravel 13 & Tailwind',
            'slug' => 'workshop-laravel-13-tailwind',
            'description' => 'Membangun aplikasi web modern dengan stack terbaru.',
            'speaker' => 'Dr. Tech Unsoed',
            'event_date' => now()->addDays(7),
            'start_time' => '09:00:00',
            'end_time' => '15:00:00',
            'location' => 'Lab Teknik Informatika',
            'quota' => 30,
            'status' => 'published'
        ]);
    }
}
