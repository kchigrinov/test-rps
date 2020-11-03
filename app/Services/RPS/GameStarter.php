<?php

namespace App\Services\RPS;

use App\Models\RpsGame;
use App\Models\RpsGameUser;
use Carbon\Carbon;

class GameStarter
{
    protected $game;
    protected $winnerPoints = 0;

    public function __construct(RpsGame $game)
    {
        $this->game = $game;
    }

    public function startGame(): RpsGame
    {
        $readyUsers = $this->game->gameUsers()->whereNotNull('gesture')->count();

        if ($readyUsers !== $this->game->slots) {
            return $this->game;
        }

        $this->getWinnerPoints();
        $this->rewardWinners();

        $this->game->started_at = Carbon::now();
        $this->game->save();

        return $this->game;
    }

    private function getWinnerPoints()
    {
        foreach ($this->game->gameUsers as $gameUser) {
            $points = $this->getUserPoints($gameUser);
            $this->winnerPoints = $points > $this->winnerPoints ? $points : $this->winnerPoints;
        }
    }

    private function getUserPoints(RpsGameUser $gameUser): int
    {
        foreach ($this->game->gameUsers->where('id', '!=', $gameUser->id) as $opponentGameUser) {
            $gameUser->points += $this->getGesturePoint($gameUser->gesture, $opponentGameUser->gesture);
        }

        return $gameUser->points;
    }

    private function getGesturePoint(string $gesture, string $opponentGesture): int
    {
        if ($gesture === $opponentGesture) {
            return 0;
        } else if ($gesture === RpsGame::GESTURE_ROCK && $opponentGesture === RpsGame::GESTURE_SCISSORS) {
            return 1;
        } else if ($gesture === RpsGame::GESTURE_ROCK && $opponentGesture === RpsGame::GESTURE_PAPER) {
            return -1;
        } else if ($gesture === RpsGame::GESTURE_SCISSORS && $opponentGesture === RpsGame::GESTURE_ROCK) {
            return -1;
        } else if ($gesture === RpsGame::GESTURE_SCISSORS && $opponentGesture === RpsGame::GESTURE_PAPER) {
            return 1;
        } else if ($gesture === RpsGame::GESTURE_PAPER && $opponentGesture === RpsGame::GESTURE_ROCK) {
            return 1;
        } else if ($gesture === RpsGame::GESTURE_PAPER && $opponentGesture === RpsGame::GESTURE_SCISSORS) {
            return -1;
        }
    }

    protected function rewardWinners()
    {
        $countWinners = $this->game->gameUsers->where('points', $this->winnerPoints)->count();

        foreach ($this->game->gameUsers as $gameUser) {
            /** @var RpsGameUser $gameUser */
            $gameUser->is_winner = $gameUser->points === $this->winnerPoints;
            $gameUser->reward = $gameUser->is_winner ? floor($this->game->cost * $this->game->slots / $countWinners) : 0;
            $gameUser->save();
        }
    }
}
