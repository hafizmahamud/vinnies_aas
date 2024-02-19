<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsImportApprovalListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations_import_approval_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('donations_file_import_id');
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
            $table->boolean('special_allocation_required')->default(false);
            $table->text('special_allocation_details')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_suburb')->nullable();
            $table->string('contact_postcode')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_mobile')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('online_donation')->nullable();
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
        Schema::dropIfExists('donations_import_approval_list');
    }
};
