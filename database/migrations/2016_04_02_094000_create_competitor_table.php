<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitor', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string("firstname", 45);
            $table->string("lastname", 45);
            $table->date("birth");
            $table->string("club", 100);
            $table->integer("ranking");
            $table->double("points", 6, 2)->default(0);
            $table->integer("startNumber");

            //Foreign Key
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')
                  ->references('id')
                  ->on('category')
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
        Schema::drop('competitor');
    }
}
