<?php

namespace App;

use Route;
use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class Donation extends Model
{
    use LogsActivity;
    use Observable;

    protected $fillable = [
        'name_on_certificate',
        'state',
        'sponsorship_value',
        'amount',
        'total_sponsorships',
        'allocated_sponsorships',
        'certificate_needed',
        'is_printed',
        'is_active',
        'received_at',
        'special_allocation_required',
        'special_allocation_details',
        'contact_address',
        'contact_suburb',
        'contact_postcode',
        'contact_email',
        'contact_phone',
        'contact_mobile',
        'online_donation',
    ];

    protected $casts = [
        'certificate_needed'          => 'boolean',
        'is_printed'                  => 'boolean',
        'is_active'                   => 'boolean',
        'special_allocation_required' => 'boolean',
        'online_donation'             => 'boolean',
    ];

    protected static $logAttributes = [
        'name_on_certificate',
        'state',
        'sponsorship_value',
        'amount',
        'total_sponsorships',
        'allocated_sponsorships',
        'certificate_needed',
        'is_printed',
        'is_active',
        'received_at',
        'special_allocation_required',
        'special_allocation_details',
        'contact_address',
        'contact_suburb',
        'contact_postcode',
        'contact_email',
        'contact_phone',
        'contact_mobile',
        'online_donation',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function getReceivedAtAttribute($value)
    {
        if (Route::currentRouteName() == 'donations.datatables') {
            return Carbon::parse($value)->format('d/m/Y');
        } elseif (Route::currentRouteName() == 'certificates.download') {
            return Carbon::parse($value)->format('Y');
        }

        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    public function getAmountAttribute($value)
    {
        if (Route::currentRouteName() == 'donations.datatables') {
            return number_format($value, 0, '.', '');
        }

        return $value;
    }

    public function isFullyAllocated()
    {
        return (
            !$this->isLesserThanSponsorshipValue() &&
            $this->total_sponsorships == $this->allocated_sponsorships &&
            $this->allocated_sponsorships > 0
        );
    }

    public function isPartiallyAllocated()
    {
        return (
            !$this->isLesserThanSponsorshipValue() &&
            $this->total_sponsorships > $this->allocated_sponsorships &&
            $this->allocated_sponsorships > 0
        );
    }

    public function isNotAllocated()
    {
        return (
            !$this->isLesserThanSponsorshipValue() &&
            $this->allocated_sponsorships == 0
        );
    }

    public function isLesserThanSponsorshipValue()
    {
        return $this->amount < $this->sponsorship_value;
    }

    public function fileImport()
    {
        return $this->belongsTo(DonationsFileImport::class, 'donations_file_import_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'name_on_certificate',
            'state',
            'sponsorship_value',
            'amount',
            'total_sponsorships',
            'allocated_sponsorships',
            'certificate_needed',
            'is_printed',
            'is_active',
            'received_at',
            'special_allocation_required',
            'special_allocation_details',
            'contact_address',
            'contact_suburb',
            'contact_postcode',
            'contact_email',
            'contact_phone',
            'contact_mobile',
            'online_donation',
        ]);
    }

    // Get attribute changes from Model for new log file
    // public static function logSubject(Donation $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
