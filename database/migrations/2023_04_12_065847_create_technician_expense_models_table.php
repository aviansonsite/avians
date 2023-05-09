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
        Schema::create('technician_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('so_id');                                            // sales order id 
            $table->string('exp_type');                                         // expense type
            $table->date('exp_date')->nullable();                               // expense date 
            $table->string('exp_desc')->nullable();                             // expense description
            $table->decimal('amount',10,2);                                     // expense amount
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
        Schema::dropIfExists('technician_expenses');
    }
};
