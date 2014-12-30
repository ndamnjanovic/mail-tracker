<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table){
      $table->increments('id');
      $table->string('email')->unique();
      $table->string('password');
      $table->string('access_token')->default('');
      $table->string('refresh_token')->default('');
      $table->string('end_of_life_token')->default('');
      $table->string('remember_token');
      $table->boolean('is_admin')->default(0);
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
		Schema::drop('users');
	}

}
