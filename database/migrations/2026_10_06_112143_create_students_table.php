<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 20);
            $table->text('address')->nullable();
            $table->longText('photo')->nullable();
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('class_id')
                ->references('id')
                ->on('classes');
            $table->foreign('parent_id')
                ->references('id')
                ->on('parents')
                ->nullOnDelete();
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
