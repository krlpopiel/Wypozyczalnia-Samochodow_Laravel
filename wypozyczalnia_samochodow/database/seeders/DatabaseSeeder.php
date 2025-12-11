<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Statusy
        $statuses = [
            ['name' => 'pending', 'label' => 'Oczekująca'],
            ['name' => 'confirmed', 'label' => 'Potwierdzona'],
            ['name' => 'ongoing', 'label' => 'W trakcie'],
            ['name' => 'completed', 'label' => 'Zakończona'],
            ['name' => 'cancelled', 'label' => 'Anulowana'],
        ];
        DB::table('rental_statuses')->insert($statuses);

        // 2. Oddziały
        DB::table('branches')->insert([
            ['name' => 'Warszawa Centrum', 'address' => 'Marszałkowska 1', 'city' => 'Warszawa', 'phone' => '22 111 22 33'],
            ['name' => 'Kraków Lotnisko', 'address' => 'Kapitana Mieczysława Medweckiego 1', 'city' => 'Kraków', 'phone' => '12 333 44 55'],
            ['name' => 'Gdańsk Główny', 'address' => 'Podwale Grodzkie 2', 'city' => 'Gdańsk', 'phone' => '58 555 66 77'],
        ]);

        // 3. Marki i Typy
        $brandIds = [];
        $brands = ['Toyota', 'BMW', 'Audi', 'Ford', 'Kia', 'Mercedes'];
        foreach($brands as $b) {
            $brandIds[] = DB::table('brands')->insertGetId(['name' => $b]);
        }

        $typeIds = [];
        $types = [
            ['name' => 'Sedan', 'slug' => 'sedan', 'base_multiplier' => 1.0],
            ['name' => 'SUV', 'slug' => 'suv', 'base_multiplier' => 1.3],
            ['name' => 'Kombi', 'slug' => 'station-wagon', 'base_multiplier' => 1.1],
            ['name' => 'Sport', 'slug' => 'sport', 'base_multiplier' => 2.0],
        ];
        foreach($types as $t) {
            $typeIds[] = DB::table('car_types')->insertGetId($t);
        }

        // 4. Cechy (Features)
        $featureIds = [];
        $features = ['Klimatyzacja', 'GPS', 'Automat', 'Podgrzewane fotele', 'Tempomat', 'Bluetooth'];
        foreach($features as $f) {
            $featureIds[] = DB::table('features')->insertGetId(['name' => $f]);
        }

        // 5. Użytkownicy (Role)
        // Admin
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@wypozyczalnia.pl',
            'role' => 'admin', // Zakładam pole 'role' w tabeli users (dodaj w migracji users jeśli nie masz)
            'password' => Hash::make('password'),
        ]);
        
        // Pracownik
        User::factory()->create([
            'name' => 'Jan Pracownik',
            'email' => 'pracownik@wypozyczalnia.pl',
            'role' => 'employee',
            'password' => Hash::make('password'),
        ]);

        // Klient
        User::factory()->create([
            'name' => 'Anna Klient',
            'email' => 'klient@wypozyczalnia.pl',
            'role' => 'client',
            'password' => Hash::make('password'),
        ]);

        // Klienci testowi
        User::factory(10)->create(['role' => 'client']);

        // 6. Samochody (30 sztuk)
        for ($i = 1; $i <= 30; $i++) {
            $carId = DB::table('cars')->insertGetId([
                'brand_id' => $brandIds[array_rand($brandIds)],
                'car_type_id' => $typeIds[array_rand($typeIds)],
                'branch_id' => rand(1, 3),
                'model' => 'Model ' . chr(rand(65, 90)) . '-' . rand(100, 900),
                'registration_plate' => 'WA ' . rand(10000, 99999),
                'year' => rand(2018, 2024),
                'color' => ['Czarny', 'Biały', 'Srebrny', 'Niebieski'][rand(0, 3)],
                'mileage' => rand(5000, 150000),
                'daily_rate' => rand(100, 500),
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Przypisz losowe cechy
            DB::table('car_feature')->insert([
                'car_id' => $carId,
                'feature_id' => $featureIds[array_rand($featureIds)]
            ]);
        }

        // 7. Przykładowe rezerwacje
        // ... (można dodać logikę tworzenia rezerwacji, ale powyższe wystarczy do startu)
    }
}