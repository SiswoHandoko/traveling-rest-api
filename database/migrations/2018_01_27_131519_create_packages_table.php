<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->text('description')->default('');
            $table->integer('days')->default(0);
            // $table->date('start_date')->default('000-00-00');
            // $table->date('end_date')->default('0000-00-00');
            // $table->bigInteger('total_price')->default(0);
            $table->string('image_url')->default('');
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
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
        Schema::dropIfExists('packages');
    }
}
