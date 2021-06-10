<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
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
            $table->longText('uuid');
            $table->longText('test');
            $table->unsignedBigInteger('orders_id');
            $table->tinyInteger('random');
            $table->longText('question_range');
            $table->foreign('orders_id')->references('id')->on('orders');
            $table->timestamp('started_on', 0);
            $table->integer('time_limit');
            $table->enum('status',['open', 'in_progress', 'done']);
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
}
