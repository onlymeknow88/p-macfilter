<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDhcpListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dhcp_lists', function (Blueprint $table) {
            $table->id();
            $table->string('mac_address')->nullable();
            $table->string('computer_name')->nullable();
            $table->string('username')->nullable();
            $table->string('lan_wan')->nullable();
            $table->string('status')->default('Block');
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
        Schema::dropIfExists('dhcp_lists');
    }
}
