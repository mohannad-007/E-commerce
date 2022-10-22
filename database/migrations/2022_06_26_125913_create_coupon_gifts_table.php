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
        Schema::create('coupon_gifts', function (Blueprint $table) {
            $table->id();
            $table->string('code',10);
            $table->ForeignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->double('money');
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
        Schema::dropIfExists('coupon_gifts');
    }
};
