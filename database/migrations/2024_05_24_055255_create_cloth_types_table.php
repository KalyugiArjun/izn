<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloth_types', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('gender')->nullable();
            $table->float('amount')->default(0);
            $table->string('taxes')->nullable();
            $table->text('note')->nullable();
            $table->integer('parent_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cloth_types');
    }
};
