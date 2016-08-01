<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string("name", 200);
            $table->integer("yearFrom");
            $table->integer("yearTo");

            //Foreign Key
            $table->integer('competition_id')->unsigned();
            $table->foreign('competition_id')
                  ->references('id')
                  ->on('competition')
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
        Schema::drop('category');
    }
}
