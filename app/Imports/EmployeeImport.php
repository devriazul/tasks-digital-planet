<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class EmployeeImport implements ToArray
{

    public static $field = [
        ['id' => 'name', 'name' => 'Name', 'required' => 'Yes',],
        ['id' => 'email', 'name' => 'Email', 'required' => 'Yes',],
        ['id' => 'mobile', 'name' => 'Mobile', 'required' => 'No',],
        ['id' => 'gender', 'name' => 'Gender', 'required' => 'No',],
        ['id' => 'employee_id', 'name' => 'Employee ID', 'required' => 'Yes'],
        ['id' => 'joining_date', 'name' => 'Joining Date', 'required' => 'No'],
        ['id' => 'address', 'name' => 'Address', 'required' => 'No'],
        ['id' => 'hourly_rate', 'name' => 'Hourly Rate', 'required' => 'No']
    ];

    public function array(array $array)
    {
        return $array;
    }

}
