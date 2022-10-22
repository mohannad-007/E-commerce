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
        Schema::create('branch_shippings', function (Blueprint $table) {
            $table->id();
            $table->ForeignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->ForeignId('shipping_id')->constrained('shippings')->cascadeOnDelete();
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
        Schema::dropIfExists('branch_shippings');
    }
};
