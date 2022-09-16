<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableApprovisionnementsChangeFournisseurId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvisionnements', function (Blueprint $table) {
            //
            $table->dropForeign('approvisionnements_fournisseur_id_foreign');
            $table->unsignedBigInteger('fournisseur_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approvisionnements', function (Blueprint $table) {
            //
        });
    }
}
