<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ActivityLog
 */
class ActivityLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('user_id')->nullable(false)->index('idx_user_id');
            $table->string('object');
            $table->uuid('object_id');

            $table->string('method');
            $table->string('endpoint');
            $table->unsignedInteger('status_code');
            $table->string('message');
            $table->ipAddress('ip_address');

            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}