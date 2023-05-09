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
        Schema::create('punch_in_out', function (Blueprint $table) {
            $table->id();
            $table->string('pin_u_id')->nullable();                                            // Punch In User Id 
            $table->string('pin_so_id')->nullable();                                           // Punch In OA Id 
            $table->date('pin_date')->nullable();                                               // Punch In date 
            $table->string('pin_remark')->nullable();                                           // Punch In remark
            $table->string('pin_latitude')->nullable();                                         // Punch In latitude
            $table->string('pin_longitude')->nullable();                                        // Punch In longitude
            $table->string('pin_img')->nullable();                                              // Punch In image
            $table->string('pout_u_id')->nullable();                                           // Punch Out User Id 
            $table->string('pout_so_id')->nullable();                                          // Punch Out OA Id 
            $table->date('pout_date')->nullable();                                              // Punch Out date 
            $table->string('pout_remark')->nullable();                                          // Punch Out remark
            $table->string('pout_work_desc')->nullable();                                       // Punch Out work_desc
            $table->string('pout_latitude')->nullable();                                        // Punch Out latitude
            $table->string('pout_longitude')->nullable();                                       // Punch Out longitude
            $table->string('pout_img')->nullable();                                             // Punch Out image
            $table->integer('a_id')->nullable();                                
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
        Schema::dropIfExists('punch_in_out');
    }
};
