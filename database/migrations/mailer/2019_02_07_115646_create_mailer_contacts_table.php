<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mailer_contacts')) {
            Schema::create('mailer_contacts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('description');
                $table->integer('user_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at');
                $table->softDeletes();
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
        Schema::dropIfExists('mailer_contacts');
    }
}
