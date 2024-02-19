<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_on_certificate');
            $table->enum('state', ['act', 'national', 'nsw', 'nt', 'qld', 'sa', 'tas', 'vic', 'wa']);
            $table->decimal('sponsorship_value', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->integer('total_sponsorships')->unsigned()->default(0);
            $table->integer('allocated_sponsorships')->unsigned()->default(0);
            $table->boolean('certificate_needed')->default(false);
            $table->boolean('is_printed')->default(false);
            $table->boolean('is_active')->default(true);
            $table->dateTime('received_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}
