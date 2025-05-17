<!-- database/migrations/2024_05_12_create_tasks_table.php -->
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('priority')->default('medium');
            $table->unsignedBigInteger('assigned_to');
            $table->unsignedBigInteger('assigned_by');
            $table->unsignedBigInteger('department_id');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('assigned_by')->references('id')->on('users');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};