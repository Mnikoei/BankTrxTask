<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->string('number', 16);
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['account_id', 'card_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
