<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('messages', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->bigIncrements('id')->autoIncrement();
            $table->unsignedBigInteger('device_id');
            $table->string('message', 200);
            $table->unsignedBigInteger('node_id');
            $table->timestamps();

            $table->index(['device_id', 'node_id']);

            $table->foreign('node_id')->references('id')->on('nodes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('device_id')->references('id')->on('devices')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}