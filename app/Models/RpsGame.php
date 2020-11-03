<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class RpsGame
 * @package App\Models
 * @property integer id
 * @property integer creator_user_id
 * @property integer slots
 * @property integer joined_users
 * @property integer cost
 * @property integer winner_user_id
 * @property Carbon started_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User creator
 * @property Collection gameUsers
 */
class RpsGame extends \Eloquent
{
    protected $dates = ['started_at'];

    const SLOTS_MIN = 3;
    const SLOTS_MAX = 3;

    const COST_MIN = 100;
    const COST_MAX = 10000;

    const GESTURE_ROCK = 'rock';
    const GESTURE_PAPER = 'paper';
    const GESTURE_SCISSORS = 'scissors';

    public function gameUsers()
    {
        return $this->hasMany(RpsGameUser::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }
}
