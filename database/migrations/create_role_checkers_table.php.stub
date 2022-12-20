<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleCheckersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_checkers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('role_checker_id');
            $table->foreign('role_checker_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unsignedInteger('role_checking_id');
            $table->foreign('role_checking_id')->references('id')->on('roles')->onDelete('cascade');
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
        Schema::dropIfExists('role_checkers');
    }
}
