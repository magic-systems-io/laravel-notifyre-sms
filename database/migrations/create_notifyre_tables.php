<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

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
            $table->string('driver', 50)->default(NotifyreDriver::SMS);
        });

        Schema::create('notifyre_sms_message_recipients', function (Blueprint $table) {
            $table->foreignId('sms_message_id')->constrained('notifyre_sms_messages')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('notifyre_recipients')->cascadeOnDelete();
            $table->boolean('sent')->default(true);
            $table->string('message', 255)->nullable();
        });

        Schema::create('notifyre_recipients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type', 255)->default(NotifyreRecipientTypes::MOBILE_NUMBER->value);
            $table->string('value', 255);

            $table->unique(['type', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifyre_recipients');
        Schema::dropIfExists('notifyre_sms_message_recipients');
        Schema::dropIfExists('notifyre_sms_messages');
    }
};
