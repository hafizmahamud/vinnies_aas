<?php

namespace App;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class Sponsorship extends Model
{
    use LogsActivity;
    use Observable;

    protected $fillable = [
        'donation_id',
        'student_id',
        'is_active'
    ];

    protected static $logAttributes = [
        'donation_id',
        'student_id',
        'is_active'
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'donation_id',
            'student_id',
            'is_active'
        ]);
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(Sponsorship $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
