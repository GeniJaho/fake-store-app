<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fake_store_id')->unique();
            $table->string('title');
            $table->unsignedInteger('price');
            $table->text('description');
            $table->string('category');
            $table->string('image');
            $table->float('rating_rate');
            $table->unsignedInteger('rating_count');
            $table->timestamps();
        });
    }
};
