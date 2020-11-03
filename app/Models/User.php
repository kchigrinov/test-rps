<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 * @property integer id
 * @property string nickname
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class User extends Authenticatable
{
    protected $fillable = ['nickname'];
}
