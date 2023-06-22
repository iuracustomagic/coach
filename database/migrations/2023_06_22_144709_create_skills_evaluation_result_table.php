<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillsEvaluationResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills_evaluation_result', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->integer('examiner_id');
            $table->integer('mark');
            $table->text('result');
            $table->text('recommendation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skills_evaluation_result');
    }
}
