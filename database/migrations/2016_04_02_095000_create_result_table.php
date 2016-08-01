<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->double("height", 6, 2)->default(0);
            $table->tinyInteger("attempt")->default(0);
            $table->tinyInteger("top")->default(0);
            $table->double("points", 6, 2)->default(0);
            $table->integer("ranking")->default(0);
            $table->text("note")->nullable();

            //Foreign Key
            $table->integer('competitor_id')->unsigned();
            $table->foreign('competitor_id')
                  ->references('id')
                  ->on('competitor')
                  ->onDelete('cascade');

            $table->integer('route_id')->unsigned();
            $table->foreign('route_id')
                  ->references('id')
                  ->on('route')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('result');
    }
}
