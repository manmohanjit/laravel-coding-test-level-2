<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->timestamps();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['NOT_STARTED', 'IN_PROGRESS', 'READY_FOR_TEST', 'COMPLETED'])->default('NOT_STARTED'); // When a task is created, status will be NOT_STARTED

            // Delete all tasks if a project is deleted
            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            // Set task to unassigned if a user is deleted
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
