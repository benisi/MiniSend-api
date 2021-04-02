<?php

use App\Models\Mail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained();
            $table->string('sender_email');
            $table->json('variables')->nullable();
            $table->string('email');
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('text')->nullable();
            $table->text('html')->nullable();
            $table->string('status')->default(Mail::STATUS_POSTED);
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
        Schema::dropIfExists('mails');
    }
}
