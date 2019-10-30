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
        Schema::create('messages', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->bigIncrements('id')->autoIncrement();
            $table->uuid('sandbox');
            $table->integer('channel_id');
            $table->string('message', 200);
            $table->dateTime('created_at')->useCurrent();

            $table->index(['sandbox', 'channel_id']);

            /*$table->foreign('channel_id')
                ->references('id')->on('channels')
                ->onDelete('cascade')
                ->onUpdate('cascade');*/

        });
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
