<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class ClientImport implements ToArray
{
    public static $field = [
        ['id' => 'name', 'name' => 'Name', 'required' => 'Yes'],
        ['id' => 'email', 'name' => 'Email', 'required' => 'Yes'],
        ['id' => 'mobile', 'name' => 'Mobile', 'required' => 'No'],
        ['id' => 'gender', 'name' => 'Gender', 'required' => 'No'],
        ['id' => 'company_name', 'name' => 'Company Name', 'required' => 'No'],
        ['id' => 'address', 'name' => 'Company Address', 'required' => 'No'],
        ['id' => 'city', 'name' => 'City', 'required' => 'No'],
        ['id' => 'state', 'name' => 'State', 'required' => 'No'],
        ['id' => 'postal_code', 'name' => 'Postal code', 'required' => 'No'],
        ['id' => 'company_phone', 'name' => 'Office Phone Number', 'required' => 'No'],
        ['id' => 'company_website', 'name' => 'Official Website', 'required' => 'No'],
        ['id' => 'gst_number', 'name' => 'GST/VAT Number', 'required' => 'No'],
    ];

    public function array(array $array)
    {
        return $array;
    }

}
