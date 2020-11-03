<?php

namespace App\Models;

/**
 * Class RpsGameUser
 * @package App\Models
 * @property integer id
 * @property integer rps_game_id
 * @property integer user_id
 * @property boolean is_winner
 * @property string gesture
 * @property integer points
 * @property integer reward
 */
class RpsGameUser extends \Eloquent
{
    protected $fillable = ['rps_game_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
