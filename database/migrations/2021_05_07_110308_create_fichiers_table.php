<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichiers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomf', 50);
            $table->string('auteur', 100);
            $table->string('lienF', 100);
            $table->string('lienA', 100);
            $table->text('description');
            $table->unsignedInteger('type');
            $table->unsignedInteger('categorie');
            $table->unsignedInteger('sous-categorie');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fichiers');
    }
}
