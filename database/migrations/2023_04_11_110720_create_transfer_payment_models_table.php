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
        Schema::create('transfer_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('u_id');                                        // user id
            $table->string('so_id');                                        // sales order id 
            $table->date('p_date')->nullable();                             // payment date 
            $table->string('p_desc')->nullable();                           // payment description
            $table->decimal('rcvd_amnt',10,2);                              // transfer amount
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
        Schema::dropIfExists('transfer_payments');
    }
};
