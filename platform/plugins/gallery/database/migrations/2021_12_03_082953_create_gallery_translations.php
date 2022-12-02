<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('galleries_translations')) {
            Schema::create('galleries_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->integer('galleries_id');
                $table->string('name', 255)->nullable();
                $table->longText('description')->nullable();

                $table->primary(['lang_code', 'galleries_id'], 'galleries_translations_primary');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('galleries_translations');
    }
};
