<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, UsesUuid;

    public $appends = ['roles'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'contact_number', 'email', 'password',
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

    public function roles() {
        return $this->belongsToMany('App\Role', 'role_users', 'user_id', 'role_id');
    }

    public function assignRole($roleId) {
        $this->roles()->sync($roleId);
        return $this->roles();
    }

    public function getRolesAttribute() {
        return $this->roles()->pluck('name');
    }

    public function hasAnyRole($roles) {
        foreach($this->roles as $r) {
            foreach($roles as $role) {
                if($role == $r)
                    return true;
            }
        }
        return false;
    }

    public function OauthAccessToken() {
        return $this->hasMany('App\OauthAccessToken');
    }

    public function logout() {
        return $this->OauthAccessToken()->delete();
    }
}
