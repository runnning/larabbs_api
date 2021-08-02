<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics',function (Blueprint $table){
            //当user_id对应users表被删除时,删除此条数据
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
        Schema::table('replies',function (Blueprint $table){
            //当user_id对应的users表数据被删除时,删除此条数据
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            //当topic_id对应的topics表数据被删除时,删除此条数据
            $table->foreign('topic_id')
                ->references('id')
                ->on('topics')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topics',function (Blueprint $table){
            //移除外键约束
            $table->dropForeign(['user_id']);
        });
        Schema::table('replies',function (Blueprint $table){
            $table->dropForeign(['user_id']);
            $table->dropForeign(['topic_id']);
        });
    }
}
