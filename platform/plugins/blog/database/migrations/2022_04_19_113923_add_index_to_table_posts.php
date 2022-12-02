<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTablePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'author_id', 'author_type', 'created_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['parent_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'author_id', 'author_type', 'created_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id', 'status', 'created_at']);
        });
    }
}
