<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_nodes', function (Blueprint $table) {
            $table->index(['parent_id', 'reference_id', 'reference_type', 'has_child', 'created_at'], 'menu_nodes_index');
        });

        Schema::table('menu_locations', function (Blueprint $table) {
            $table->index(['menu_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_nodes', function (Blueprint $table) {
            $table->dropIndex('menu_nodes_index');
        });

        Schema::table('menu_locations', function (Blueprint $table) {
            $table->dropIndex(['menu_id', 'created_at']);
        });
    }
}
