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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->text('fileable_id');
            $table->text('fileable_type');
            $table->uuid()->unique();
            $table->string('display_name')->nullable();
            $table->string('disk');
            $table->string('filepath');
            $table->string('filename');
            $table->string('mimetype');
            $table->unsignedBigInteger('size');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['disk', 'filepath']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
