<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return new class {
    public function up()
    {
        if (!Capsule::schema()->hasTable('posts')) {
            Capsule::schema()->create('posts', function ($table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Capsule::schema()->hasTable('posts')) {
            Capsule::schema()->drop('posts');
        }
    }
};