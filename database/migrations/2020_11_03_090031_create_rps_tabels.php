<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpsTabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nickname', 32)->unique();
            $table->timestamps();
        });

        Schema::create('rps_games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('creator_user_id');
            $table->unsignedTinyInteger('slots');
            $table->unsignedTinyInteger('joined_users')->default(0);
            $table->unsignedInteger('cost');
            $table->dateTime('started_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('rps_game_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('rps_game_id')->index();
            $table->unsignedInteger('user_id');
            $table->string('gesture', 16)->nullable();
            $table->tinyInteger('points')->default(0);
            $table->boolean('is_winner')->default(false);
            $table->unsignedInteger('reward')->default(0);
            $table->timestamps();

            $table->unique(['rps_game_id', 'user_id']);
        });

        $i = 0;
        $faker = Faker\Factory::create();

        do {
            $name = strtolower($faker->firstName);

            try {
                User::create(['nickname' => $name]);
            } catch (\Exception $e) {
                continue;
            }

            $i++;
        } while ($i < 10);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('rps_games');
        Schema::dropIfExists('rps_game_users');
    }
}
