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
        Schema::create('oa_tl_history', function (Blueprint $table) {
            $table->id();
            $table->integer('so_id');                                   // assign OA id  
            $table->integer('lead_technician');                         // user id (lead technician)
            $table->integer('status')->default('1');                    // OA-active : 1  , OA-inactive :0   
            $table->integer('a_id');                                    // project admin id
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
        Schema::dropIfExists('oa_tl_history');
    }
};
