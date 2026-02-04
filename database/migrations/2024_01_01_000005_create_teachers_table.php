<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('subject_specialization');
            $table->date('hire_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['school_id', 'employee_id']);
            $table->index(['school_id', 'subject_specialization']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};