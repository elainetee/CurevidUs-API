<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Staudenmeir\LaravelMergedRelations\Eloquent\HasMergedRelationships;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasMergedRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role_id',
        'emergency_contact_person_id',
        'email',
        'tel_no',
        'password',
        'quarantine_status',
        'vac_status',
        'avatar',
        'quarantine_day',
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function userName()
    {
        return $this->name;
    }

    public function avatar()
    {
        return $this->avatar;
    }

    public function condition()
    {
        return $this->hasMany(Condition::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function publicRooms()
    {
        return $this->hasMany(PublicRoom::class);
    }

    //friend module
    public function friend()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id');
    }

    public function friendsTo()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->withPivot('accepted')
            ->withTimestamps();
    }

    public function friendsFrom()
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->withPivot('accepted')
            ->withTimestamps();
    }

    public function pendingFriendsTo()
    {
        return $this->friendsTo()->wherePivot('accepted', false);
    }

    public function pendingFriendsFrom()
    {
        return $this->friendsFrom()->wherePivot('accepted', false);
    }

    public function acceptedFriendsTo()
    {
        return $this->friendsTo()->wherePivot('accepted', true);
    }

    public function acceptedFriendsFrom()
    {
        return $this->friendsFrom()->wherePivot('accepted', true);
    }

    public function friends()
    {
        return $this->mergedRelationWithModel(User::class, 'friends_view');
    }
}
