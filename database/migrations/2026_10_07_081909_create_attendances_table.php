<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('class_id');
            $table->date('date');
            $table->enum('status', [
                'present',
                'absent',
                'late',
                'permission'
            ]);
            
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->restrictOnDelete();

            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->restrictOnDelete();

            // prevent duplicate attendance
            $table->unique([
                'student_id',
                'class_id',
                'date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};