<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_class_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->timestamps();
            
            $table->unique(['teacher_id', 'class_id', 'subject']);
            $table->index(['teacher_id']);
            $table->index(['class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_class_assignments');
    }
};