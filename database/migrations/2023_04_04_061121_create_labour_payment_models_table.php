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
        Schema::create('labour_payments', function (Blueprint $table) {
            $table->id();
            $table->string('u_id');                                             // labour id
            $table->string('so_id');                                            // sales order
            $table->string('p_desc')->nullable();                               // payment description
            $table->date('payment_date')->nullable();                           // payment date 
            $table->decimal('payment_amnt',10,2);                               // labour payment amount
            $table->integer('created_by');                                      // accountant id
            $table->tinyInteger('delete')->default('0');
            $table->integer('a_id');
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
        Schema::dropIfExists('labour_payments');
    }
};
