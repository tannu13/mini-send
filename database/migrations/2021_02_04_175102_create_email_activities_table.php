<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sender', 100);
            $table->string('recipient', 100);
            $table->string('subject', 100);
            $table->longText('text_content')->nullable();
            $table->longText('html_content')->nullable();
            $table->string('attachments', 500)->nullable();
            $table->string('status', 10); // skipping on evil enums['posted', 'sent', 'failed'])->default('posted');
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
        Schema::dropIfExists('email_activities');
    }
}
