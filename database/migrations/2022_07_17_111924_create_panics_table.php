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
        Schema::create('panics', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('longtitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('panic_type')->nullable();
            $table->mediumText('details')->nullable();
            $table->integer('is_cancel')->default(0);
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
        Schema::dropIfExists('panics');
    }
};
