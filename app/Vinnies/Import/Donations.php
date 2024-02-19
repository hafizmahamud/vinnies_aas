<?php

namespace App\Vinnies\Import;

use Illuminate\Database\QueryException;
use App\DonationsImportApprovalList;
use App\DonationsFileImport;
use Carbon\Carbon;
use App\Donation;
use App\Treshold;
use PDOException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Donations extends BaseImport
{
    public $invalidRow;

    protected $columns = [
        'Donation State',
        'Donation Received Date-Time',
        'Donor Name on Certificate',
        'Donation Amount (AUD)',
        'Donor requires certificate',
        'Special allocation required?',
        'Special allocation Details',
        'Address',
        'City/Suburb',
        'Post Code',
        'Email',
        'Phone',
        'Mobile',
        'Online Donation',
    ];

    public function validState($state)
    {
        $state  = strtolower($state);
        $states = collect(
            $this->csv->fetchColumn(
                $this->index('Donation State')
            ),
            false
        );

        $states = $states->map(function ($state) {
            return strtolower($state);
        })->filter()->unique();

        // Must contain single state only
        if ($states->count() > 2) {
            return false;
        }

        return $states->first() === $state;
    }

    public function validName()
    {
        $rows = collect(
            $this->csv->fetchColumn(
                $this->index('Donor Name on Certificate')
            ),
            false
        );

        $rows->each(function ($row, $key) {
          if (empty($row)) {
                $this->invalidRow = $key + 1;
                return false;
            }
        });

        return empty($this->invalidRow);
    }

    public function validAddress()
    {
        $rows = collect(
            $this->csv->fetchColumn(
                $this->index('Address')
            ),
            false
        );

        $rows->each(function ($row, $key) {
          if (empty($row)) {
                $this->invalidRow = $key + 1;
                return false;
            }
        });

        return empty($this->invalidRow);
    }

    public function hasDuplicates()
    {
        $donations = $this->data->take(5)->all();

        foreach ($donations as $donation) {
            $duplicate = Donation::where('name_on_certificate', $donation['Donor Name on Certificate'])
                ->where('is_active', 1)
                ->where('received_at', $this->getReceivedAtDt($donation['Donation Received Date-Time']))
                ->where('amount', $donation['Donation Amount (AUD)'])
                ->first();

            if ($duplicate) {
                return true;
            }
        }

        return false;
    }

    public function hasDuplicateFile($file)
    {
        $duplicate = DonationsFileImport::where('file', $file)
            ->first();

        if ($duplicate) {
            return true;
        }

        return false;
    }

    private function getReceivedAtDt($received_at)
    {
        if (strpos($received_at, ':') === false) {
            $received_at = $received_at . ' 00:00:00';
        } else {
            $received_at = $received_at . ':00';
        }

        return Carbon::createFromFormat('d/m/Y H:i:s', $received_at);
    }

    public function validSpecialDetailsRequired()
    {
        $rows = collect(
            $this->csv->fetchColumn(
                $this->index('Special allocation required?')
            ),
            false
        );

        $rows->each(function ($row, $key) {
            if (!empty($row) && !in_array(strtolower($row), ['yes', 'no'])) {
                $this->invalidRow = $key + 1;
                return false;
            }
        });

        return empty($this->invalidRow);
    }

    public function validSpecialDetails()
    {
        $this->data->filter()->each(function ($donation, $key) {
            if (!empty($donation['Special allocation required?']) && strtolower($donation['Special allocation required?']) == 'yes' && strlen($donation['Special allocation Details']) < 3) {
                $this->invalidRow = $key + 1;
                return false;
            }
        });

        return empty($this->invalidRow);
    }

    public function import($file_id)
    {
        $value = Treshold::current();
        $data  = $this->data->filter()->map(function ($donation) use ($value,$file_id) {
            if (is_numeric($donation['Donation Amount (AUD)'])) {
                $amount = $donation['Donation Amount (AUD)'];
            } else {
                $amount = $this->removeUnessacaryChar($donation['Donation Amount (AUD)']);
            }

            $data = [
                'name_on_certificate'         => utf8_encode($donation['Donor Name on Certificate']),
                'donations_file_import_id'    => $file_id,
                'state'                       => strtolower($donation['Donation State']),
                'sponsorship_value'           => $value->amount,
                'amount'                      => (double)$amount,
                'certificate_needed'          => (strtolower($donation['Donor requires certificate']) == 'yes' ? 1 : 0),
                'received_at'                 => $this->getReceivedAtDt($donation['Donation Received Date-Time']),
                'special_allocation_required' => (strtolower($donation['Special allocation required?']) == 'yes' ? 1 : 0),
                'special_allocation_details'  => $donation['Special allocation Details'] ?? null,
                'contact_address'             => utf8_encode($donation['Address']) ?? null,
                'contact_suburb'              => utf8_encode($donation['City/Suburb']) ?? null,
                'contact_postcode'            => $donation['Post Code'] ?? null,
                'contact_email'               => $donation['Email'] ?? null,
                'contact_phone'               => $donation['Phone'] ?? null,
                'contact_mobile'              => $donation['Mobile'] ?? null,
                'online_donation'             => (strtolower($donation['Online Donation']) == 'yes' ? 1 : 0),
                'created_at'                  => Carbon::now(),
                'updated_at'                  => Carbon::now()
            ];

            $data['total_sponsorships'] = floor($data['amount'] / $data['sponsorship_value']);

            return $data;
        });

        try {
            $result = DonationsImportApprovalList::insert($data->toArray());
        } catch (QueryException $e) {
            Log::error(
                'Fail to insert donation into database',
                [
                    'e'    => $e
                ]
            );
        } catch (PDOException $e) {
            Log::error(
                'Fail to insert donation into database',
                [
                    'e'    => $e
                ]
            );
        }

        if ($result) {
            return [
                'count' => $data->count()
            ];
        } else {
            return false;
        }
    }

    /**
     * this function basically remove unessacary character 
     * to avoid failing convert string to double
     * 
     * @param string $amount
     * 
     * @return string
     */
    private function removeUnessacaryChar(string $amount) : string
    {
        $chars = ['$', ','];

        return Str::replace($chars, '', $amount);
    }
}
