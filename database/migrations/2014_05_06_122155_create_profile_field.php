<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('profile_field');
        Schema::create('profile_field', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('profile_id')->unsigned();
            $table->integer('profile_field_type_id')->unsigned();
            $table->string('value');
            // relations
            $table->foreign('profile_id')
                  ->references('id')->on('user_profile')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreign('profile_field_type_id')
                  ->references('id')->on('profile_field_type')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            // indexes
            $table->unique(['profile_id','profile_field_type_id']);
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
		Schema::drop('profile_field');
	}

}
