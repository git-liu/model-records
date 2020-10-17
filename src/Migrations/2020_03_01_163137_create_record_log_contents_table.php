<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordLogContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_log_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('log_id')->comment('变更记录id（关联record_logs.id）');
            $table->string('field')->comment('变更字段名');
            $table->string('current_value')->nullable()->comment('字段当前值');
            $table->string('original_value')->nullable()->comment('字段原始值');
            $table->string('field_type')->comment('字段类型');
            $table->timestamps();
            
            $table->index(['log_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('record_log_contents');
    }
}
