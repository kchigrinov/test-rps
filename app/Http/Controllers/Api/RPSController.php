<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RPS\CreateGameRequest;
use App\Http\Requests\RPS\GestureGameRequest;
use App\Http\Resources\RPS\GameResource;
use App\Models\RpsGame;
use Illuminate\Http\Request;

class RPSController extends Controller
{
    /**
     * @api {get} /api/rps/game/{gameId} Get game
     * @apiDescription Get game
     * @apiName Get game
     * @apiGroup Rps Game
     *
     * @param RpsGame $game
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getGame(RpsGame $game)
    {
        return response()->json(['game' => new GameResource($game)]);
    }

    /**
     * @api {get} /api/rps/list Get opened games
     * @apiDescription Get list of opened games
     * @apiName Get opened games
     * @apiGroup Rps Game
     * @apiParam {Integer{0-50}} limit Default 5
     * @apiParam {Integer} offset Default 0
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getList(Request $request)
    {
        $limit = abs($request->get('limit', 5));
        $limit = $limit > 50 ? 50 : $limit;
        $offset = abs($request->get('offset', 0));

        $games = RpsGame::query()
            ->whereNull('started_at')
            ->limit($limit)
            ->offset($offset)
            ->orderByDesc('id')
            ->with(['creator', 'gameUsers.user'])
            ->get();

        return response()->json(['games' => GameResource::collection($games)]);
    }

    /**
     * @api {get} /api/rps/history Get games history
     * @apiDescription Get list of finished games
     * @apiName Get finished games
     * @apiGroup Rps Game
     * @apiParam {Integer{0-50}} limit Default 5
     * @apiParam {Integer} offset Default 0
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getHistory(Request $request)
    {
        $limit = abs($request->get('limit', 5));
        $limit = $limit > 50 ? 50 : $limit;
        $offset = abs($request->get('offset', 0));

        $games = RpsGame::query()
            ->whereNotNull('started_at')
            ->limit($limit)
            ->offset($offset)
            ->orderByDesc('id')
            ->with(['creator', 'gameUsers.user'])
            ->get();

        return response()->json(['games' => GameResource::collection($games)]);
    }

    /**
     * @api {post} /api/rps/create Create game
     * @apiDescription Create new game
     * @apiName Create new game
     * @apiGroup Rps Game
     * @apiParam {String} nickname User's nickname
     * @apiParam {Integer={100-10000}} cost Game cost to join
     * @apiParam {Integer={3-3}} slots Max slots in game
     *
     * @param CreateGameRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function postCreate(CreateGameRequest $request)
    {
        $game = \RPSService::create(\Auth::user(), (int)$request->get('cost'), (int)$request->get('slots'));

        return response()->json(['game' => new GameResource($game)]);
    }

    /**
     * @api {post} /api/rps/join/{gameId} Join game
     * @apiDescription Join game
     * @apiName Join game
     * @apiGroup Rps Game
     * @apiParam {String} nickname User's nickname
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function postJoin(RpsGame $game)
    {
        $game = \RPSService::join($game, \Auth::user());

        return response()->json(['game' => new GameResource($game)]);
    }

    /**
     * @api {post} /api/rps/gesture/{gameId} Set gesture
     * @apiDescription Sets gesture for the game
     * @apiName Set gesture
     * @apiGroup Rps Game
     * @apiParam {String} nickname User's nickname
     * @apiParam {String} gesture Sets gesture for the game
     *
     * @param GestureGameRequest $request
     * @param RpsGame $game
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function postGesture(GestureGameRequest $request, RpsGame $game)
    {
        $game = \RPSService::setGesture($game, \Auth::user(), $request->get('gesture'));

        return response()->json(['game' => new GameResource($game)]);
    }

    /**
     * @api {post} /api/rps/quit/{gameId} Quit game
     * @apiDescription Quit game
     * @apiName Quit game
     * @apiGroup Rps Game
     * @apiParam {String} nickname User's nickname
     *
     * @param RpsGame $game
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function postQuit(RpsGame $game)
    {
        $game = \RPSService::quit($game, \Auth::user());

        return response()->json(['game' => new GameResource($game)]);
    }
}
