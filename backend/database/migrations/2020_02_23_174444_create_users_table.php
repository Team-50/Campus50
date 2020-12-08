<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();            
            $table->string('username')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('nomor_hp')->unique();
            $table->year('ta')->default(0);
            $table->integer('code')->default(0);        
            $table->string('theme')->default('default');
            $table->string('foto')->default('storage/images/users/no_photo.png');
            $table->boolean('active')->default(1);
            $table->boolean('isdeleted')->default(1);
            $table->string('default_role',15);
            $table->boolean('locked')->default(0);                          
            
            $table->timestamps();
            $table->index('ta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
