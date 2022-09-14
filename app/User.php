<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \App\Models\UserRoles;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'firstname', 'lastname', 'user_type', 'avatar', 'admin_role', 'status'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['avatar_url'];

    public function admin_role_data()
    {
        return $this->hasOne(UserRoles::class, 'slug', 'admin_role');
    }

    public function getNameAttribute($value)
    {
        return ucfirst($this->attributes['firstname']) . ' ' . ucfirst($this->attributes['lastname']);
    }

    public function getAvatarAttribute($value)
    {
        if (empty($this->attributes['avatar'])) {
            return '';
        }
        $path = str_replace('/', DIRECTORY_SEPARATOR, public_path($this->attributes['avatar']));
        return file_exists($path) &&  !is_dir($path) ? $this->attributes['avatar'] : '';
    }

    public function getAvatarUrlAttribute($value)
    {
        $path = $this->getAvatarAttribute($value);
        return asset('/public/' . ($path == '' ? 'images/default/no-image-available.png' : $path));
    }
}
