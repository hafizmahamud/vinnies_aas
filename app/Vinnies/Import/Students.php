<?php

namespace App\Vinnies\Import;

use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Student;
use PDOException;
use Log;

class Students extends BaseImport
{
    public $invalidRow;

    protected $columns = [
        'Student Assistance Year',
        'Student Firstname',
        'Student Lastname',
        'Student Class',
        'Student Country',
        'Education Sector',
        'Age',
        'Gender',
    ];

    public function validAssistanceYear($year)
    {
        $year  = (int) $year;
        $years = collect(
            $this->csv->fetchColumn(
                $this->index('Student Assistance Year')
            ),
            false
        );

        $years = $years->map(function ($year) {
            return (int) $year;
        })->filter()->unique();

        // Must contain single year only
        if ($years->count() > 2) {
            return false;
        }

        return $years->first() === $year;
    }

    public function validAge()
    {
        $ages = collect(
            $this->csv->fetchColumn(
                $this->index('Age')
            ),
            false
        );

        $row = $ages->each(function ($age, $key) {
            if (!empty($age) && !is_numeric($age)) {
                $this->invalidRow = $key + 1;
                return false;
            }
        });

        return empty($this->invalidRow);
    }

    public function validGender()
    {
        $genders = collect(
            $this->csv->fetchColumn(
                $this->index('Gender')
            ),
            false
        );

        $row = $genders->each(function ($gender, $key) {
            if (!empty($gender) && !in_array(strtolower($gender), ['male', 'female'])) {
                $this->invalidRow = $key + 1;
                return false;
            }
        });

        return empty($this->invalidRow);
    }

    public function validEducationSector()
    {
        $sector = collect(
            $this->csv->fetchColumn(
                $this->index('Education Sector')
            ),
            false
        );

        $row = $sector->each(function ($sector, $key) {
            if (!empty($sector) && !in_array(strtolower($sector), ['primary', 'secondary', 'tertiary', '', 'na', 'n/a'])) {
                $this->invalidRow = $key + 1;
                return false;
            }
        });

        return empty($this->invalidRow);
    }

    public function import($file_id = null)
    {
        $data = $this->data->filter()->map(function ($student) {
            if (empty($education_sector = $student['Education Sector'])) {
                $education_sector = 'N/A';
            }

            if (empty($gender = $student['Gender'])) {
                $gender = 'N/A';
            } else {
                $gender = ucwords($student['Gender']);
            }

            return [
                'first_name'       => utf8_encode($student['Student Firstname']),
                'last_name'        => utf8_encode($student['Student Lastname']),
                'assistance_year'  => $student['Student Assistance Year'],
                'class'            => $student['Student Class'],
                'country'          => $student['Student Country'],
                'education_sector' => $education_sector,
                'age'              => empty($student['Age']) ? null : (int) $student['Age'],
                'gender'           => $gender,
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now()
            ];
        });

        $result = false;

        try {
            $result = Student::insert($data->toArray());
        } catch (QueryException $e) {
            Log::error(
                'Fail to insert student into database',
                [
                    'e'    => $e
                ]
            );
        } catch (PDOException $e) {
            Log::error(
                'Fail to insert student into database',
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
}
