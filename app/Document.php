<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class Document extends Model
{
    use LogsActivity;
    use Observable;

    protected $fillable = [
        'path',
        'type',
        'user_id',
        'comments',
        'documentable_id',
        'documentable_type',
    ];

    protected $casts = [
        'user_id'         => 'integer',
        'documentable_id' => 'integer',
    ];

    protected static $logAttributes = [
        'path',
        'type',
        'user_id',
        'comments',
        'documentable_id',
        'documentable_type',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
        'path',
        'type',
        'user_id',
        'comments',
        'documentable_id',
        'documentable_type',
        ]);
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(Document $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
