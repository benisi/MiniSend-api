<?php

use App\Models\Batch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('subject');
            $table->foreignId('user_id')->constrained();
            $table->text('text')->nullable();
            $table->text('html')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default(Batch::STATUS_UNCOMPLETE);
            $table->integer('recipient_count');
            $table->integer('pending_mail');
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
        Schema::dropIfExists('batches');
    }
}
