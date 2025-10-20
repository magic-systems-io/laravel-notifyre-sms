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
            $table->string('id')->primary();
            $table->string('sender', 50)->nullable()->index();
            $table->string('body', 160)->nullable();
            $table->string('driver', 50)->default(NotifyreDriver::SMS)->index();
            $table->timestamps();
        });

        Schema::create('notifyre_recipients', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('type', 255)->default(NotifyreRecipientTypes::MOBILE_NUMBER->value);
            $table->string('value', 255);
            $table->timestamps();

            $table->unique(['type', 'value']);
            $table->index(['value', 'type']);
        });

        Schema::create('notifyre_sms_message_recipients', function (Blueprint $table) {
            $table->string('sms_message_id');
            $table->string('recipient_id');
            $table->boolean('sent')->default(false);

            $table->foreign('sms_message_id')->references('id')->on('notifyre_sms_messages')->cascadeOnDelete();
            $table->foreign('recipient_id')->references('id')->on('notifyre_recipients')->cascadeOnDelete();
            $table->unique(['sms_message_id', 'recipient_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('notifyre_recipients');
        Schema::dropIfExists('notifyre_sms_message_recipients');
        Schema::dropIfExists('notifyre_sms_messages');
    }
};
