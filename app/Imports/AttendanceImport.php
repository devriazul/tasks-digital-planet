<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class AttendanceImport implements ToArray
{
    public static $field = [
        ['id' => 'email', 'name' => 'Email', 'required' => 'Yes'],
        ['id' => 'clock_in_time', 'name' => 'Clock In Time', 'required' => 'Yes'],
        ['id' => 'clock_out_time', 'name' => 'Clock Out Time', 'required' => 'No'],
        ['id' => 'clock_in_ip', 'name' => 'Clock In Ip', 'required' => 'No'],
        ['id' => 'clock_out_ip', 'name' => 'Clock Out Ip', 'required' => 'No'],
        ['id' => 'working_from', 'name' => 'Working From', 'required' => 'No'],
        ['id' => 'late', 'name' => 'Late', 'required' => 'No'],
        ['id' => 'half_day', 'name' => 'Half Day', 'required' => 'No']
    ];

    public function array(array $array)
    {
        return $array;
    }

}

