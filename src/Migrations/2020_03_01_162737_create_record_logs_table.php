<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('tb_logs')) {
            Schema::create('tb_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('account_id')->comment('操作的用户id');
                $table->string('table_name', 128)->comment('变更表名称');
                $table->integer('table_id')->comment('变更数据id');
                $table->string('origin_table_name')->comment('原始表名');
                $table->integer('origin_table_id')->comment('原始表数据id');
                $table->string('comment', 512)->nullable()->comment('变更原因');
                $table->string('title', 512)->nullable()->comment('变更标题');
                $table->string('modify_type')->default('Column')->comment('变更类型（Column:字段变更 Operate:数据变更）');
                $table->string('model')->comment('表模型');
                $table->timestamps();
                
                $table->index(['table_name', 'table_id']);
                $table->index(['account_id']);
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
        Schema::dropIfExists('tb_logs');
    }
}
