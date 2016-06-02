<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHashToFilerLocalFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('filer_local_files', function (Blueprint $table) {
            $table->string('hash')->nullable()->after('size')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('filer_local_files', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
    }
}
