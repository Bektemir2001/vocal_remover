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
        Schema::create('audio', function (Blueprint $table) {
            $table->id();
            $table->string('origin_name');
            $table->string('path');
            $table->string('audio_voice')->nullable();
            $table->string('audio_noise')->nullable();
            $table->jsonb('text')->nullable();
            $table->unsignedBigInteger('users_id');
            $table->unsignedSmallInteger('status')->default(0);
            $table->timestamps();
            $table->index('users_id');
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio');
    }
};
