<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShoppingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->tinyInteger('items_count')->unsigned();
            $table->decimal('total_amount', 8, 2);
            $table->string('status', 20);
            $table->timestamps();
        });

        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cart_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->string('item_type', 20);
            $table->decimal('item_price', 8, 2);
            $table->tinyInteger('item_quantity')->unsigned();
            $table->decimal('item_total', 8, 2);
            $table->string('status', 20);
            $table->timestamps();
        });

        Schema::create('shop_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('cart_id')->unsigned();
            $table->tinyInteger('items_count')->unsigned();
            $table->decimal('total_amount', 8, 2);
            $table->string('status', 20);
            $table->timestamps();
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->string('item_type', 20);
            $table->decimal('item_price', 8, 2);
            $table->tinyInteger('item_quantity')->unsigned();
            $table->decimal('item_total', 8, 2);
            $table->string('status', 20);
            $table->timestamps();
        });

        Schema::create('shop_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->string('payment_gateway', 20);
            $table->string('transaction_id', 255);
            $table->string('status', 20);
            $table->timestamps();
        });

        Schema::create('user_passes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('reserve_pass_id')->unsigned();
            $table->boolean('is_owner');
            $table->string('pass_photo', 255);
            $table->decimal('pass_amount', 8, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20);
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
        Schema::drop('shop_carts');
        Schema::drop('shop_cart_items');
        Schema::drop('shop_orders');
        Schema::drop('shop_order_items');
        Schema::drop('shop_payments');
        Schema::drop('user_passes');
    }
}
