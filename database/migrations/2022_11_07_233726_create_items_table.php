<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('list_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->boolean('completed')->default(false);
            $table->timestamps();

            // One-to-many on lists table
            $table->foreign('list_id')
            ->references('id')
            ->on('item_lists')
            ->onDelete('cascade');

            // One-to-many on projects table
            $table->foreign('project_id')
            ->references('id')
            ->on('projects')
            ->onDelete('cascade');

            // One-to-many on projects table
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
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
        Schema::dropIfExists('items');
    }
};
