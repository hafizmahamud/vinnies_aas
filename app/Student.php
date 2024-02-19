<?php

namespace App;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class Student extends Model
{
    use LogsActivity;
    use Observable;

    protected $fillable = [
        'first_name',
        'last_name',
        'assistance_year',
        'class',
        'country',
        'education_sector',
        'age',
        'gender',
        'is_allocated',
        'is_active'
    ];

    protected $casts = [
        'assistance_year' => 'integer',
        'age'             => 'integer',
        'is_allocated'    => 'boolean',
        'is_active'       => 'boolean',
    ];

    protected static $logAttributes = [
        'first_name',
        'last_name',
        'assistance_year',
        'class',
        'country',
        'education_sector',
        'age',
        'gender',
        'is_allocated',
        'is_active'
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function sponsorship()
    {
        return $this->hasOne(Sponsorship::class);
    }

    public function getFullName()
    {
        $name = [$this->first_name, $this->last_name];
        $name = array_filter($name);

        return implode(' ' , $name);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'first_name',
            'last_name',
            'assistance_year',
            'class',
            'country',
            'education_sector',
            'age',
            'gender',
            'is_allocated',
            'is_active'
        ]);
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(Student $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
