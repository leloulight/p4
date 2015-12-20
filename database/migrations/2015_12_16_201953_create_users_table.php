<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
                Schema::create('users', function($table)
                {
                        $table->increments('id');
                        $table->string('firstname');
                        $table->string('lastname');
                        $table->string('username');
                        $table->string('email', 64);
                        $table->boolean('validated');
                        $table->string('password');
                        $table->string('remember_token');
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
