<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('number')->unique();
            $table->timestamps();

            $table->unique(['user_id', 'number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};