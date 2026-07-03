<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('user_masters', function ($table) {

        $table->string('smtp_password')
              ->nullable()
              ->after('email');

    });
}

public function down()
{
    Schema::table('user_masters', function ($table) {

        $table->dropColumn('smtp_password');

    });
}
};
