<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the user's gravatar picture.
     *
     * @return string
     */
    public function getGravatarUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email));
    }

    /**
     * Get posts belonging to this user.
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    /**
     * Get this user's followers.
     *
     * @param  string  $value
     * @return BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany('App\User', 'followers', 'user_id', 'follower_user_id');
    }

    /**
     * Get the users who are being followed by the current.
     *
     * @param  string  $value
     * @return BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany('App\User', 'followers', 'follower_user_id', 'user_id');
    }

    /**
     * Return wether the user is a follower or not.
     *
     * @param  string  $value
     * @return BelongsToMany
     */
    public function isFollower($id)
    {
        if ($this->followers->where('id', '=', $id)->count() > 0) {
            return true;
        }
        return false;
    }
}
