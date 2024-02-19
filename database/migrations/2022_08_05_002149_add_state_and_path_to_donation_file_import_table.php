<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::table('donations_file_import', function (Blueprint $table) {
             $table->enum('state', ['act', 'national', 'nsw', 'nt', 'qld', 'sa', 'tas', 'vic', 'wa'])->after('file');
             $table->string('path')->nullable()->after('file');
         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::table('donations_file_import', function (Blueprint $table) {
             $table->dropColumn(['state', 'path']);
         });
     }
};
