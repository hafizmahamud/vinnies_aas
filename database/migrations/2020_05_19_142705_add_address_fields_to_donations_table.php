<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressFieldsToDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('contact_mobile')->nullable()->after('special_allocation_details');
            $table->string('contact_phone')->nullable()->after('special_allocation_details');
            $table->string('contact_email')->nullable()->after('special_allocation_details');
            $table->string('contact_postcode')->nullable()->after('special_allocation_details');
            $table->string('contact_suburb')->nullable()->after('special_allocation_details');
            $table->string('contact_address')->nullable()->after('special_allocation_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['contact_mobile', 'contact_phone', 'contact_email', 'contact_address', 'contact_suburb', 'contact_postcode']);
        });
    }
}
