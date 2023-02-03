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
        Schema::table('links', function (Blueprint $table)
        {
            $table->string('secret_key')->nullable()->change();
            $table->timestamp('created_at')->useCurrent()->change();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('links', function (Blueprint $table)
        {
            $table->string('secret_key')->change();            
            $table->timestamp('created_at')->useCurrent()->change();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->change();
        });
        
    }
};
