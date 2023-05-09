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
        Schema::table('technician_expenses', function (Blueprint $table) {
            $table->integer('acc_id')->nullable()->after('so_id');                          // Accountant Id 
            $table->string('acc_remark')->nullable()->after('exp_desc');                    // Accountant Remark
            $table->string('status')->default('Uncleared')->after('a_id');                  // Status
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('technician_expenses', function (Blueprint $table) {
            //
        });
    }
};
