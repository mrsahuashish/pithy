<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function ($table) {
                $table->string('name')->nullable()->change();
                $table->string('email')->nullable()->change();
                $table->string('password')->nullable()->change();
                $table->string('provider_name')->nullable()->after('password');
                $table->string('provider_id')->nullable()->after('password');
                $table->text('login_oauth_uid')->nullable()->after('provider_name');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('provider_id');
            $table->dropColumn('provider_name');
            $table->dropColumn('google_access_token_json');
            $table->string('name')->change();
            $table->string('email')->unique()->change();
            $table->string('password')->change();
        });
    }
};
