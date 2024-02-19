<?php

namespace App;

use App\Vinnies\Helper;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class Gallery extends Model
{
    use LogsActivity;
    use Observable;

    protected $fillable = [
        'file',
        'year',
        'country',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'year'       => 'integer',
        'updated_by' => 'integer',
    ];

    protected static $logAttributes = [
        'file',
        'year',
        'country',
        'description',
        'updated_by',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public static function rules()
    {
        return [
            'file' => 'required',
            'year' => [
                'required',
                'digits:4',
                Rule::in(array_keys(Helper::getGalleryYears())),
            ],
            'country' => [
                'required',
                Rule::in(Helper::getGalleryCountries()),
            ],
            'description' => 'required',
        ];
    }

    public function getUpdatedByAttribute($value)
    {
        return User::find($value);
    }

    public function getSizeAttribute()
    {
        return Helper::formatFileSize(File::size(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->file)));
    }

    public function getUrlAttribute()
    {
        return url('storage' . DIRECTORY_SEPARATOR . $this->file);
    }

    public function getExtensionAttribute()
    {
        $exts = (explode('.', $this->file));

        return array_pop($exts);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'file',
            'year',
            'country',
            'description',
            'updated_by',
        ]);
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(Gallery $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
