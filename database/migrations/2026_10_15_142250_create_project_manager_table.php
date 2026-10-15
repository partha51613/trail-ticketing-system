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
        Schema::create('project_manager', function (Blueprint $table) {

        // cascadeOnDelete works only when an actual DELETE statement is run on the actual table i.e 'users' or 'projects'

        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
        $table->timestamps();
        $table->primary(['user_id','project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_manager');
    }
};
