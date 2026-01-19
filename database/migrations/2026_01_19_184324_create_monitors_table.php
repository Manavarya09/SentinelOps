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
        Schema::create('monitors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->enum('method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])->default('GET');
            $table->json('headers')->nullable();
            $table->json('body')->nullable();
            $table->unsignedInteger('interval')->default(5); // minutes
            $table->unsignedInteger('timeout')->default(30); // seconds
            $table->unsignedInteger('retries')->default(3);
            $table->unsignedInteger('failure_threshold')->default(3);
            $table->boolean('is_active')->default(true);
            $table->boolean('check_ssl')->default(true);
            $table->unsignedInteger('response_time_threshold')->nullable(); // ms
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
