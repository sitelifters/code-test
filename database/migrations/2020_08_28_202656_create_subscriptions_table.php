<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Store info about subscriptions.
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Intermediate table to store each user's subscriptions.
        Schema::create('users_subscriptions', function (Blueprint $table) {
            // Add foreign key constraints and ensure that record automatically gets deleted when its User or Product gets deleted.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // First drop the foreign key constraints on the table so we don't get errors when we go to delete the table.
        Schema::table('users_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subscription_id']);
        });

        Schema::dropIfExists('users_subscriptions');
        Schema::dropIfExists('subscriptions');
    }
}
