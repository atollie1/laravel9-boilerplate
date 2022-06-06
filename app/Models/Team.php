<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Team",
 *     type="object",
 *
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="name",
 *          type="string",
 *          example="House of Stark"
 *     ),
 *
 *     @OA\Property(
 *          property="code",
 *          type="string",
 *          example="house-of-stark"
 *     ),
 *
 *     @OA\Property(
 *          property="created_by",
 *          type="object",
 *          @OA\Property(
 *              property="id",
 *              type="integer",
 *              example="1"
 *          ),
 *          @OA\Property(
 *              property="name",
 *              type="string",
 *              example="admin"
 *          ),
 *     ),
 *
 *     @OA\Property(
 *          property="updated_by",
 *          type="object",
 *          @OA\Property(
 *              property="id",
 *              type="integer",
 *              example="1"
 *          ),
 *          @OA\Property(
 *              property="name",
 *              type="string",
 *              example="admin"
 *          ),
 *     ),
 *
 *     @OA\Property(
 *          property="created_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 *
 *     @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     )
 * )
 */
class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'created_by',
        'updated_by',
    ];
}
