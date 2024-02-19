<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class DonationsImportApprovalList extends Model
{
    use Observable;
    
    protected $table = 'donations_import_approval_list';

    protected $fillable = [
        'name_on_certificate',
        'donations_file_import_id',
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
        'donations_file_import_id',
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

    public function getReceivedAtAttribute($value)
    {

        if (Route::currentRouteName() != 'donations.import-approve') {
          return Carbon::parse($value)->format('d/m/Y H:i');
        }

        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'name_on_certificate',
            'donations_file_import_id',
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
    // public static function logSubject(DonationsImportApprovalList $model): string
    // {
    //     return sprintf("User [id:%d] %s/%s",
    //         $model->id, $model->name, $model->email
    //     );
    // }
}
