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
        Schema::table('company_profile', function (Blueprint $table) {
            $table->decimal('bike_pkm_rate',10,2)->nullable()->after('ifsc_code');                            // bike per km rate
            $table->decimal('car_pkm_rate',10,2)->nullable()->after('bike_pkm_rate');                            // bike per km rate
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_profile', function (Blueprint $table) {
            //
        });
    }
};
