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
        Schema::create('company_profile', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('website');
            $table->string('company_email');
            $table->string('account_email');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->integer('pincode');
            $table->string('pan_number');
            $table->string('pan_file')->nullable();
            $table->string('gst_number');
            $table->string('gst_file')->nullable();
            $table->string('iec_code');
            $table->string('logo')->nullable();
            $table->string('iso_certificate_number');
            $table->string('iso_file')->nullable();
            $table->string('primary_mobile');
            $table->string('alternate_mobile');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('branch');
            $table->string('bank_name');
            $table->string('ifsc_code');
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
        Schema::dropIfExists('company_profile');
    }
};
