<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('relations', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->bigIncrements('id')->autoIncrement();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('node_id');
            $table->timestamps();

            $table->unique(['device_id', 'group_id', 'node_id']);

            //$table->primary('sandbox');
            $table->index(['device_id', 'group_id', 'node_id']);

            $table->foreign('node_id')->references('id')->on('nodes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('device_id')->references('id')->on('devices')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('group_id')->references('id')->on('groups')
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
        Schema::dropIfExists('relations');
    }
}