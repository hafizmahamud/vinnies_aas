<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecialAllocationFieldsToDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->boolean('special_allocation_required')->default(false)->after('is_active');
            $table->text('special_allocation_details')->nullable()->after('special_allocation_required');
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
            $table->dropColumn(['special_allocation_required', 'special_allocation_details']);
        });
    }
}
