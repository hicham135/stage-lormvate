<!-- database/migrations/2024_05_12_create_reports_table.php -->
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('created_by');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};