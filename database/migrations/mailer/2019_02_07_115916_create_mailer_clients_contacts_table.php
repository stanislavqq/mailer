<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailerClientsContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mailer_contacts_lists_contacts')) {
            Schema::create('mailer_contacts_lists_contacts', function (Blueprint $table) {
                $table->integer('contact_id');
                $table->integer('list_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailer_contacts_lists_contacts');
    }
}
