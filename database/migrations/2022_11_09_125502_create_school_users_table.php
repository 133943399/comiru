<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_users', function (Blueprint $table) {
            $table->bigInteger('sid');
            $table->bigInteger('uid');
            $table->tinyInteger('type')->default(0)->common('0:普通学生,1:管理员,2:普通教师');
            $table->unique(['sid', 'uid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_users');
    }
}
