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
        Schema::create('students', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->primary();
            $table->string('name');
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->integer('class_id');
            $table->integer('parent_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('class_id')
                ->references('id')
                ->on('classes');

            $table->foreign('parent_id')
                ->references('id')
                ->on('parents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
