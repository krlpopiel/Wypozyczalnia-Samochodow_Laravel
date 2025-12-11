<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela oddziałów
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('phone');
            $table->timestamps();
        });

        // 2. Marki samochodów
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // np. Toyota, BMW
            $table->string('country')->nullable();
            $table->timestamps();
        });

        // 3. Typy nadwozia/klasy
        Schema::create('car_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // np. SUV, Sedan, Kombi
            $table->string('slug')->unique();
            $table->decimal('base_multiplier', 3, 2)->default(1.0); // Mnożnik ceny
            $table->timestamps();
        });

        // 4. Statusy rezerwacji (Słownik)
        Schema::create('rental_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // pending, confirmed, completed, cancelled
            $table->string('label'); // Oczekujące, Potwierdzone...
            $table->timestamps();
        });

        // 5. Samochody (Główna tabela)
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('car_type_id')->constrained();
            $table->foreignId('branch_id')->constrained(); // Lokalizacja auta
            $table->string('model');
            $table->string('registration_plate')->unique();
            $table->integer('year');
            $table->string('color');
            $table->integer('mileage');
            $table->decimal('daily_rate', 8, 2); // Cena za dzień
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // 6. Wyposażenie (opcje)
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Klimatyzacja, GPS, Automat
            $table->string('icon_class')->nullable(); // np. dla FontAwesome
            $table->timestamps();
        });

        // 7. Tabela łącząca samochody z wyposażeniem (Many-to-Many)
        Schema::create('car_feature', function (Blueprint $table) {
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->primary(['car_id', 'feature_id']);
        });

        // 8. Rezerwacje (Rentals)
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Klient
            $table->foreignId('car_id')->constrained();
            $table->foreignId('rental_status_id')->constrained();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('total_price', 10, 2);
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // 9. Płatności
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status'); // paid, pending, failed
            $table->string('method'); // card, transfer, cash
            $table->timestamps();
        });

        // 10. Opinie (Reviews)
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('car_id')->constrained();
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Kolejność usuwania ważna ze względu na klucze obce
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('rentals');
        Schema::dropIfExists('car_feature');
        Schema::dropIfExists('features');
        Schema::dropIfExists('cars');
        Schema::dropIfExists('rental_statuses');
        Schema::dropIfExists('car_types');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('branches');
    }
};