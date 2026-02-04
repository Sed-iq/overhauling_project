<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id')->unique();
            $table->date('date_of_birth');
            $table->string('guardian_name');
            $table->string('guardian_phone');
            $table->text('address');
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['school_id', 'student_id']);
            $table->index(['school_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};