<?php

/**
 * This file is a simplified mock of Spatie\Permission\Models\group
 */

namespace RobTrehy\LaravelAzureProvisioning\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * A group belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_groups',
            'group_id',
            'model_id'
        );
    }

    public static function findByName(string $name)
    {
        $group = static::where('name', $name)->first();

        return $group;
    }

    public static function findById(int $id)
    {
        $group = static::where('id', $id)->first();

        return $group;
    }

    public static function findOrCreate(string $name)
    {
        $group = static::where('name', $name)->first();

        if (! $group) {
            return static::query()->create(['name' => $name]);
        }

        return $group;
    }
}
