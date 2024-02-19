<?php

namespace App\Vinnies;

use Illuminate\Support\Facades\Auth;

class Helper
{
    public static function getStates()
    {
        return [
            'act'      => 'Australian Capital Territory',
            'nsw'      => 'New South Wales',
            'nt'       => 'Northern Territory',
            'qld'      => 'Queensland',
            'sa'       => 'South Australia',
            'tas'      => 'Tasmania',
            'vic'      => 'Victoria',
            'wa'       => 'Western Australia',
            'national' => 'National'
        ];
    }

    public static function getUserStates()
    {
        $user = Auth::user();

        if ($user->state !== 'national') {
            $key = $user->state;
            $state = self::getStateNameByKey($key);
            return [$key => $state];
        }

        return self::getStates();
    }

    public static function getStateNameByKey($key = false)
    {
        $states = self::getStates();
        if (array_key_exists($key, self::getStates())) {
            return $states[$key];
        }

        return false;
    }

    public static function getStateKeyByName($name)
    {
        $name = strtolower($name);

        foreach (self::getStates() as $key => $state) {
            $state = strtolower($state);

            if (strpos($state, $name) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function asset($path)
    {
        if (!env('APP_DEBUG')) {
            $path = str_replace(['.css', '.js'], ['.min.css', '.min.js'], $path);
        }

        return sprintf(
            '%s?v=%s',
            asset($path),
            filemtime(public_path($path))
        );
    }

    public static function getRowIds($data)
    {
        return array_map(function ($data) {
            return (int) str_replace('row_', '', $data);
        }, $data);
    }

    public static function getDocUrl($key)
    {
        return url('/docs/' . $key);
    }

    public static function getEducationSectors()
    {
        return [
            'Primary'   => 'Primary',
            'Secondary' => 'Secondary',
            'Tertiary'  => 'Tertiary',
            'N/A'       => 'N/A'
        ];
    }

    public static function getGenders()
    {
        return [
            'Male'   => 'Male',
            'Female' => 'Female',
            'N/A'    => 'N/A'
        ];
    }

    public static function getGalleryYears()
    {
        return collect(range(2010, date('Y')))->mapWithKeys(function ($year) {
            return [$year => $year];
        })->toArray();
    }

    public static function getGalleryCountries()
    {
        return collect([
            'India',
            'Indonesia',
            'Kiribati',
            'Myanmar',
            'Thailand',
            'Generic',
        ])->mapWithKeys(function ($country) {
            return [$country => $country];
        })->toArray();;
    }

    // http://php.net/manual/en/function.filesize.php#106569
    public static function formatFileSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }



    public static function getDocumentTypes()
    {
        return [
            'correspondence'              => 'Correspondence', //rename old
            'project_application'         => 'Projects – Project Application', //rename old
            'signed_cover_sheet'          => 'Projects – Project Application Cover Sheet Signed', //rename old
            'project_progress_report'     => 'Projects – Project Progress Report', //new option
            'project_completion_report'   => 'Projects – Project Completion Report', //rename old
            'status_check_request'        => 'Twinning – Status Check Request', //rename old
            'surrender_notification'      => 'Twinning – Surrender Notification', //rename old
            'aggregation_certificate'     => 'Conf. Status – Aggregation Certificate', //rename old
            'abeyance_certificate'        => 'Conf. Status – Abeyance Certificate', //new option
            'other'                       => 'Other',
            'twinning_payments'           => 'Twinning Payments', // no new name provided
            'grants_payments'             => 'Grants Payments', // no new name provided
            'council_to_council_payments' => 'Council to Council Payments', // no new name provided
            'project_payments'            => 'Project Payments', // no new name provided
        ];
    }

    public static function getDocumentTypesOption()
    {
        return [
            ''                            => 'Please select', //rename old
            'correspondence'              => 'Correspondence', //rename old
            'project_application'         => 'Projects – Project Application', //rename old
            'signed_cover_sheet'          => 'Projects – Project Application Cover Sheet Signed', //rename old
            'project_progress_report'     => 'Projects – Project Progress Report', //new option
            'project_completion_report'   => 'Projects – Project Completion Report', //rename old
            'status_check_request'        => 'Twinning – Status Check Request', //rename old
            'surrender_notification'      => 'Twinning – Surrender Notification', //rename old
            'aggregation_certificate'     => 'Conf. Status – Aggregation Certificate', //rename old
            'abeyance_certificate'        => 'Conf. Status – Abeyance Certificate', //new option
            'other'                       => 'Other',
        ];
    }
}
