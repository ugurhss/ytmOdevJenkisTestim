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
        Schema::create('group_announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id'); // Hangi gruba ait
            $table->unsignedBigInteger('user_id');  // Kim tarafından oluşturuldu
            $table->string('title');
            $table->text('content');
            $table->timestamps();

            // Foreign key tanımlamaları
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_announcements');
    }
};
