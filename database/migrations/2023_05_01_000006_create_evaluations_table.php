<!-- database/migrations/2024_05_12_create_evaluations_table.php -->
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluated_user_id');
            $table->unsignedBigInteger('evaluator_id');
            $table->string('period');
            $table->integer('performance_score');
            $table->integer('communication_score');
            $table->integer('teamwork_score');
            $table->integer('innovation_score');
            $table->text('comments')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            
            $table->foreign('evaluated_user_id')->references('id')->on('users');
            $table->foreign('evaluator_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};