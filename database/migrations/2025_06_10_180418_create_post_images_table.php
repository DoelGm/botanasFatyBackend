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
        Schema::create('post_images', function (Blueprint $table) {
           $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // FK a products.id
            $table->string('cloudinary_url');
            $table->string('cloudinary_public_id'); // ID para borrar o actualizar en Cloudinary
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_images');
    }
};
