<?php

namespace App\Services\RPS;

use App\Models\RpsGame;
use App\Models\RpsGameUser;
use App\Models\User;

class Service
{
    public function create(User $user, int $cost, int $slots = 3): RpsGame
    {
        // TODO Games limit for user?

        $game = new RpsGame();
        $game->creator_user_id = $user->id;
        $game->cost = $cost;
        $game->slots = $slots;
        $game->save();

        return $game;
    }

    public function join(RpsGame $game, User $user): RpsGame
    {
        \DB::beginTransaction();

        $updated = RpsGame::where('id', $game->id)
            ->whereRaw('joined_users < slots')
            ->increment('joined_users');

        if (!$updated) {
            throw new \RuntimeException('No available slots in the game.');
        }

        try {
            RpsGameUser::create([
                'rps_game_id' => $game->id,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException('You already joined to this game.');
        }

        \DB::commit();

        $game->load('gameUsers.user');

        return $game;
    }

    public function setGesture(RpsGame $game, User $user, string $gesture): RpsGame
    {
        if (!is_null($game->started_at)) {
            throw new \RuntimeException('Game is finished.');
        }

        \DB::beginTransaction();

        /** @var RpsGame $game */
        $game = RpsGame::where('id', $game->id)->lockForUpdate()->first();

        $updated = $game->gameUsers()->where('user_id', $user->id)
            ->whereNull('gesture')
            ->update([
                'gesture' => $gesture
            ]);

        if (!$updated) {
            throw new \RuntimeException('You cannot set the gesture!');
        }

        $game = (new GameStarter($game))->startGame();

        \DB::commit();

        $game->load('gameUsers.user');

        return $game;
    }

    public function quit(RpsGame $game, User $user): RpsGame
    {
        \DB::beginTransaction();

        $updated = RpsGame::where('id', $game->id)
            ->whereNull('started_at')
            ->where('joined_users', '>', 0)
            ->decrement('joined_users');

        if (!$updated) {
            throw new \RuntimeException('You cannot quit from the started game!');
        }

        $updated = $game->gameUsers()->where('user_id', $user->id)->delete();

        if (!$updated) {
            throw new \RuntimeException('You are not in the game!');
        }

        \DB::commit();

        $game->load('gameUsers.user');

        return $game;
    }
}
