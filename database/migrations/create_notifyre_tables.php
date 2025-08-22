<?php

use Arbi\Notifyre\Enums\NotifyreRecipientTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::create('notifyre_sms_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('messageId', 255);
            $table->string('sender', 50)->nullable();
            $table->string('body', 160)->nullable();
        });

        Schema::create('notifyre_sms_message_recipients', function (Blueprint $table) {
            $table->foreignId('notifyre_sms_message_id')->constrained('notifyre_sms_messages')->cascadeOnDelete();
            $table->foreignId('notifyre_recipient_id')->constrained('notifyre_recipients')->cascadeOnDelete();
        });

        Schema::create('notifyre_recipients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('type', NotifyreRecipientTypes::values())->default(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER);
            $table->string('value', 255);

            $table->unique(['type', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifyre_sms_messages');
    }
};
