<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return Config::get('foxhound.storage.database.connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('foxhound_manifests', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('channel');
            $table->boolean('unread')->default(true);
            $table->timestamp('sent_at');
            $table->binary('html');
            $table->binary('attachments');
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foxhound_manifests');
    }
};
