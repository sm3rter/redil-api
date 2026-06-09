<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return new class {
    public function up()
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('api_token')->unique()->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Capsule::schema()->hasTable('users')) {
            Capsule::schema()->drop('users');
        }
    }
};