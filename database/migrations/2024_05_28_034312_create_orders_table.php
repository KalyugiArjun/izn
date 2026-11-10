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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->default(0);
            $table->integer('customer_id')->default(0);
            $table->date('order_date')->nullable();
            $table->date('deadline_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('febric')->nullable();
            $table->string('febric_color')->nullable();
            $table->string('gender')->nullable();
            $table->integer('responsible')->default(0);
            $table->integer('cloth_type')->default(0);
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->text('measurement')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
