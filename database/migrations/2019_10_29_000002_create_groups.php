<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->bigIncrements('id')->autoIncrement();
            $table->string('name', 30);
            $table->string('key', 64)->nullable();
            $table->unsignedBigInteger('node_id');
            $table->timestamps();

            $table->unique(['key']);
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
        Schema::dropIfExists('groups');
    }
}