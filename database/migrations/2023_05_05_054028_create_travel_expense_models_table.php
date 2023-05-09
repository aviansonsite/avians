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
        Schema::create('travel_expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('ad_id')->nullable();                                   // admin Id 
            $table->integer('sa_id')->nullable();                                   // super admin Id 
            $table->string('mode_travel');                                          // mode of travel
            $table->string('from_location');                                        // from location
            $table->string('to_location');                                          // to location
            $table->decimal('total_km',10,2);                                       // total km
            $table->date('travel_date');                                            // travel date 
            $table->string('travel_desc')->nullable();                              // travel desc (for technician)
            $table->string('ad_remark')->nullable();                                // admin remark
            $table->string('sa_remark')->nullable();                                // sa remark
            $table->string('attachment')->nullable();                               // travel attachment
            $table->decimal('travel_amount',10,2);                                  // travel amount
            $table->string('status')->default('Uncleared');                         // travel expense Status
            $table->integer('a_id')->nullable();                                    // technician id 
            $table->tinyInteger('delete')->default('0');
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
        Schema::dropIfExists('travel_expenses');
    }
};
