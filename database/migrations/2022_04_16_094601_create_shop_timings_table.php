<?php

use App\Models\Shops;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_timings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Shops::class, 'storeId');
            $table->string('days');
            $table->string('startTime')->nullable();
            $table->string('endTime')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=>days, 2=>breaks');
            $table->tinyInteger('is_open')->default(1)->comment('1=>On, 0=>Off');
            $table->tinyInteger('breakStatus');
            $table->softDeletes();
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
        Schema::dropIfExists('shop_timings');
    }
}