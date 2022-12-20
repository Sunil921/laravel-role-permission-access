<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('row_id');
            $table->string('table_name');
            $table->string('module_name');
            $table->boolean('operation')->comment('0 for create and 1 for update');
            $table->string('link');
            $table->integer('added_by');
            $table->enum('approve', [-1, 0, 1])->default(0)->comment('-1 for reject, 0 for pending and 1 for success');
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('approvals');
    }
}
