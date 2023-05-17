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
        Schema::create('survey_models', function (Blueprint $table) {
            $table->id();
            $table->string("slug")->nullable();
            $table->string("survey_key")->unique();
            $table->integer("row_id")->nullable();
            $table->string("method")->default("list");
            $table->timestamp("starts_at")->nullable();
            $table->timestamp("ends_at")->nullable();
            $table->text("options")->nullable();
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
        Schema::dropIfExists('survey_models');
    }
};
