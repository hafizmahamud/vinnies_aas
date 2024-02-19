<?php

namespace App;

// use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Traits\HasGoogle2FA;
use Spatie\Activitylog\LogOptions;
use App\Notifications\ResetPassword;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Traits\Observable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasRoles;
    // use SoftDeletes;
    use Notifiable;
    use HasGoogle2FA;
    use LogsActivity;
    use CanResetPassword;
    use Observable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'state',
        'branch_display',
        'is_new',
        'has_accepted_terms',
        'has_accepted_conditions',
        'conditions_accepted_at',
        'google2fa_secret',
        'google2fa_enabled_at',
        'last_login',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_new'               => 'boolean',
        'has_accepted_terms'   => 'boolean',
        'has_accepted_conditions'   => 'boolean',
        'conditions_accepted_at'    => 'datetime',
        'google2fa_enabled_at' => 'datetime',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function getState()
    {
        if ($this->state == 'national') {
            return ucwords($this->state);
        }

        return strtoupper($this->state);
    }

    public function getLastLoginDt()
    {
        if (empty($this->last_login)) {
            return false;
        }

        return new Carbon($this->last_login);
    }

    public function getFullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
        'first_name',
        'last_name',
        'email',
        //'password',
        'state',
        'branch_display',
        'is_new',
        'has_accepted_terms',
        'has_accepted_conditions',
        'conditions_accepted_at',
        'google2fa_secret',
        'google2fa_enabled_at',
        'last_login',
        'is_active',
        ])->logOnlyDirty();
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(User $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
