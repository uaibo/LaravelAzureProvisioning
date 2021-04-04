<?php

namespace RobTrehy\LaravelAzureProvisioning\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function groups()
    {
        return $this->morphToMany(
            Group::class,
            'model',
            'model_has_groups',
            'model_id',
            'group_id'
        );
    }

    public function assignRole($name)
    {
        $group = Group::findByName($name);
        $this->groups()->attach($group);
    }

    public function removeRole($name)
    {
        $group = Group::findByName($name);
        $this->groups->detach($group);
    }
}
