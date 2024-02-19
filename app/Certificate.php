<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class Certificate extends Model
{
    use Observable;

    protected $fillable = [
        'data'
    ];

    protected static $logAttributes = [
        'data'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'data',
        ]);
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(Certificate $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
