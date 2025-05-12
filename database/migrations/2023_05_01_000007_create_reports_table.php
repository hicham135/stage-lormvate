<?php
// database/migrations/2023_05_01_000007_create_reports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['attendance', 'performance', 'tasks', 'general'])->default('general');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->json('parameters')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};