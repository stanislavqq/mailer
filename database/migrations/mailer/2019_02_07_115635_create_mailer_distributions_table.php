<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailerDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mailer_distributions')) {
            Schema::create('mailer_distributions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('active')->default(0);
                $table->string('name');
                $table->string('subject');
                $table->string('description');
                $table->string('from_email');
                $table->string('from_name')->nullable();
                $table->integer('contacts_list_id');
                $table->integer('template_id');
                $table->integer('send_by_date')->default(0);
                $table->timestamp('send_date')->useCurrent();
                $table->timestamp('last_send_date')->nullable();
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
        Schema::dropIfExists('mailer_distributions');
    }
}
