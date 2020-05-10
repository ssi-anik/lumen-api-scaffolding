<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailedJobsTable extends Migration
{
    public function up () {
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        if (!Schema::hasColumn('failed_jobs', 'exception')) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->longText('exception')->nullable();
            });
        }
    }

    public function down () {
        Schema::dropIfExists('failed_jobs');
    }
}
