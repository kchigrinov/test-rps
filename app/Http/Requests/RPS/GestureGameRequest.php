<?php
namespace App\Http\Requests\RPS;

use App\Models\RpsGame;
use Illuminate\Foundation\Http\FormRequest;

class GestureGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'gesture' => 'required|in:' . implode(',', [RpsGame::GESTURE_ROCK, RpsGame::GESTURE_PAPER, RpsGame::GESTURE_SCISSORS])
        ];
    }
}


