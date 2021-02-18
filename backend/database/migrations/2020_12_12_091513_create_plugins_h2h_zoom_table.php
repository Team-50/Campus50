<?php
/**
 * tabel ini digunakan untuk menyimpan daftar mahasiswa yang telah dimigrasikan datanya
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePluginsH2hZoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('plugins_h2h_zoom', function (Blueprint $table) {
            $table->uuid('id')->primary();            
            $table->string('email')->unique();
            $table->string('zoom_id')->nullable();            
            $table->string('api_key');
            $table->string('api_secret');
            $table->string('im_token');
            $table->string('jwt_token')->nullable();

            $table->tinyInteger('status')->default(0);

            $table->string('desc')->nullable();
            
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
        Schema::dropIfExists('plugins_h2h_zoom');
    }
}
