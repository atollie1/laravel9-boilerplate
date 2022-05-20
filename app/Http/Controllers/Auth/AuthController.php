<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="auth")
 */
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['authUser', 'logout']);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="v1Login",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", format="email", example="john.snow@stark.com"),
     *              @OA\Property(property="password", type="string", example="secret"),
     *              @OA\Property(property="device_name", type="string", example="web")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Login success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="token", type="string"),
     *                  @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Login failed",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="401"),
     *                  @OA\Property(property="message", type="string", example="The provided credentials are incorrect.")
     *              )
     *          )
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => 'The provided credentials are incorrect.',
                ]
            ], 400);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user,
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     operationId="v1Logout",
     *     tags={"auth"},
     *     @OA\Response(
     *          response=204,
     *          description="Logout success"
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/user",
     *     operationId="v1GetAuthUser",
     *     tags={"auth"},
     *     @OA\Response(
     *          response=200,
     *          description="Authenticated user",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authUser(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }
}
