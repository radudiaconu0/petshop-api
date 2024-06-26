<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('category_id');
            $table->uuid('uuid');
            $table->string('title');
            $table->float('price');
            $table->text('description');
            $table->json('metadata');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')->references('uuid')->on('categories');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
