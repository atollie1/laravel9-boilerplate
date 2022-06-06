<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(name="Team")
 */
class TeamController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/api/teams",
     *     operationId="v1GetTeams",
     *     tags={"Team"},
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          example="10"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="available options: id, name, code, created_at, updated_at",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_dir",
     *          description="available options: asc, desc",
     *          example="desc"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Team List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="current_page",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Team")
     *              ),
     *              @OA\Property(
     *                  property="first_page_url",
     *                  type="string",
     *                  example="/api/teams?page=1"
     *              ),
     *              @OA\Property(
     *                  property="from",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="last_page",
     *                  type="integer",
     *                  example="10"
     *              ),
     *              @OA\Property(
     *                  property="last_page_url",
     *                  type="string",
     *                  example="/api/teams?page=10"
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="url",
     *                          type="string",
     *                          nullable=true,
     *                          example=null
     *                      ),
     *                      @OA\Property(
     *                          property="label",
     *                          type="string",
     *                          example="&laqou; Previous"
     *                      ),
     *                      @OA\Property(
     *                          property="active",
     *                          type="boolean",
     *                          example="false"
     *                      ),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="next_page_url",
     *                  type="string",
     *                  example="/api/teams?page=2"
     *              ),
     *              @OA\Property(
     *                  property="path",
     *                  type="string",
     *                  example="/api/roles"
     *              ),
     *              @OA\Property(
     *                  property="per_page",
     *                  type="integer",
     *                  example="10"
     *              ),
     *              @OA\Property(
     *                  property="prev_page_url",
     *                  type="string",
     *                  example="null"
     *              ),
     *              @OA\Property(
     *                  property="to",
     *                  type="integer",
     *                  example="10"
     *              ),
     *              @OA\Property(
     *                  property="total",
     *                  type="integer",
     *                  example="100"
     *              ),
     *          )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);

        $teams = Team::orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return response()->json($teams);
    }

    /**
     * @OA\Post(
     *     path="/api/teams",
     *     operationId="v1CreateTeam",
     *     tags={"Team"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="The House of Stark"),
     *              @OA\Property(property="code", type="string", example="the-house-of-stark"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Role created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Team")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Team data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create Team",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create Team")
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\CreateTeamRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeamRequest $request)
    {
        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $team = Team::create([
            'name' => $request->name,
            'code' => $request->code,
            'created_by' => $userJson,
            'updated_by' => $userJson,
        ]);

        if (!$team) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to create Team',
                ],
            ], 500);
        }

        return response()->json([
            'data' => $team,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/teams/{teamId}",
     *     operationId="v1GetTeamDetail",
     *     tags={"Team"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="teamId",
     *          description="The id of the team",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Team detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Team")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Team not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find team with id {teamId}")
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find team with id {$id}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => $team,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/teams/{teamId}",
     *     operationId="v1UpdateTeam",
     *     tags={"Team"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="teamId",
     *          description="The id of the team",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="The House of Stark")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Team updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Team")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Team not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find team with id {teamId}")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Team data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create Team",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create Team")
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\UpdateTeamRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeamRequest $request, $id)
    {
        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find team with id {$id}",
                ],
            ], 404);
        }

        $updated = $team->update([
            'name' => $request->name,
            'updated_by' => $userJson,
        ]);

        if (!$updated) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to update Team",
                ],
            ], 500);
        }

        return response()->json([
            'data' => $team,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/teams/{teamId}",
     *     operationId="v1DeleteTeam",
     *     tags={"Team"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="teamId",
     *          description="The id of the team",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Team deleted",
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Team not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find team with id {teamId}")
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find team with id {$id}",
                ],
            ], 404);
        }

        $team->delete();

        return response(null, 204);
    }
}
