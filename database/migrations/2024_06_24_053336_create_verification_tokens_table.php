<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('verification_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->index();
            $table->string('token', 10)->index();
            $table->timestamp('expires_at')->nullable();
            $table->index(['email', 'token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('verification_tokens');
    }
};
