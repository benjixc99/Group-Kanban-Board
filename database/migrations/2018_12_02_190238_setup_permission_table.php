<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetupPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Throwable
     */
    public function up()
    {
        try {
            DB::beginTransaction();

            // Create table for storing roles
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->uuid('uid')->unique();
                $table->string('name')->unique();
                $table->timestamps();
            });

            // Create table for storing permissions
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->uuid('uid')->unique();
                $table->unsignedBigInteger('role_id');
                $table->string('name');
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // Handle Error
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permissions');
        Schema::drop('roles');
    }
}
