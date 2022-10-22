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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->ForeignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->ForeignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->String('name_of_product');
            $table->Integer('quantity')->default(1);
            $table->Integer('viewer')->default(0);
            $table->Double('price');
            $table->Integer('likes')->default(0);
            $table->Double('rate')->default(1);
            $table->Date('date_of_production');
            $table->Date('booked_at')->default(null);
            $table->Date('buy_at')->default(null);
            $table->Integer('discount');
            $table->Integer('number_of_sales')->default(0);
            $table->Boolean('booked')->default(false);
            $table->String('new_or_old');
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
        Schema::dropIfExists('products');
    }
};
