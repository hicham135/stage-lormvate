<?php
// database/migrations/2023_05_01_000006_create_evaluations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluatee_id')->constrained('users')->onDelete('cascade');
            $table->string('period');
            $table->integer('performance_score')->default(0);
            $table->integer('punctuality_score')->default(0);
            $table->integer('teamwork_score')->default(0);
            $table->integer('initiative_score')->default(0);
            $table->text('comments')->nullable();
            $table->text('goals')->nullable();
            $table->enum('status', ['draft', 'submitted', 'acknowledged'])->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};
