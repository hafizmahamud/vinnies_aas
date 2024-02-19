<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class DonationsFileImport extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use Observable;

    protected $table = 'donations_file_import';

    protected $fillable = [
        'file',
        'user_id',
        'is_approved',
        'approved_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'approved_at',
    ];

    protected $casts = [
        'user_id'         => 'integer',
        'is_approved'           => 'boolean',
    ];

    protected static $logAttributes = [
        'file',
        'user_id',
        'is_approved',
        'approved_at',
    ];

    public function donation()
    {
        return $this->hasMany(Donation::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'file',
            'user_id',
            'is_approved',
            'approved_at',
        ]);
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(DonationsFileImport $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
