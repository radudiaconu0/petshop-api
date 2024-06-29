<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jwt_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('unique_id');
            $table->string('token_title');
            $table->json('restrictions');
            $table->json('permissions');
            $table->timestamp('expires_at');
            $table->timestamp('last_used_at');
            $table->timestamp('refreshed_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jwt_tokens');
    }
};
