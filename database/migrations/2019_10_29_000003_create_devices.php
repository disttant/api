<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('devices', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->bigIncrements('id')->autoIncrement();
            $table->string('name', 30);
            $table->string('type', 50)->nullable();
            $table->string('description', 50)->nullable();
            $table->unsignedBigInteger('node_id');
            $table->timestamps();

            $table->unique(['name', 'node_id']);
            $table->index(['name', 'node_id']);

            $table->foreign('node_id')->references('id')->on('nodes')
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
        Schema::dropIfExists('devices');
    }
}