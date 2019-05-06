<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailerTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mailer_templates')) {
            Schema::create('mailer_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('description');
                $table->text('body');
                $table->integer('author_id');
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
        Schema::dropIfExists('mailer_templates');
    }
}
