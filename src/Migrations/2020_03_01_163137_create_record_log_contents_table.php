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
        if (! Schema::hasTable('tb_log_contents')) {
            Schema::create('tb_log_contents', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('log_id')->comment('变更记录id（关联record_logs.id）');
                $table->string('tb_key')->comment('变更字段名');
                $table->string('tb_zh_key')->nullable()->comment('变更字段名中文');
                $table->longText('current_tb_value')->nullable()->comment('字段当前值');
                $table->longText('tb_value')->nullable()->comment('字段原始值');
                $table->string('field_type')->comment('字段类型');
                $table->string('model')->comment('被变更的数据模型');
                $table->timestamps();
        
                $table->index(['log_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_log_contents');
    }
}
