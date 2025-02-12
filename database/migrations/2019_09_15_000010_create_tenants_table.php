<?php

declare(strict_types=1);

use App\Enums\TenantEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->enum('status', TenantEnum::asArray())->default(TenantEnum::Provisioning());
            $table->unsignedBigInteger('metropole_id');
            $table->foreign('metropole_id')->references('id')->on('metropoles')->onDelete('cascade')->onUpdate('cascade');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
