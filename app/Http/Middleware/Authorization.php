<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()->messages()
            ], 403);
        }

        $user = User::where('nickname', $request->get('nickname'))->first();

        if (!$user) {
            throw new \RuntimeException('User not found!', 400);
        }

        \Auth::setUser($user);

        return $next($request);
    }
}
