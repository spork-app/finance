<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('feature_list_id')->unsigned();
            // Plaid's unique identifier for the account
            $table->string('account_id')->index();
            $table->string('mask')->nullable();
            $table->string('name')->nullable()->index();
            $table->string('official_name')->nullable();
            $table->double('balance')->default(0.0);
            $table->double('available')->nullable();
            $table->string('subtype')->nullable();
            $table->string('type');
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
        Schema::dropIfExists('accounts');
    }
}
