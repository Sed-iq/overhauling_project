<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('grade_level');
            $table->integer('capacity')->default(30);
            $table->timestamps();
            
            $table->unique(['school_id', 'name']);
            $table->index(['school_id', 'grade_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};