<?php
namespace App\Http\Requests\RPS;

use App\Models\RpsGame;
use Illuminate\Foundation\Http\FormRequest;

class CreateGameRequest extends FormRequest
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
            'cost' => 'required|integer|min:' . RpsGame::COST_MIN . '|max:' . RpsGame::COST_MAX,
            'slots' => 'required|integer|min:' . RpsGame::SLOTS_MIN . '|max:' . RpsGame::SLOTS_MAX,
        ];
    }
}


