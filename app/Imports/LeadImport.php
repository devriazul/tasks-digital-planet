<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class LeadImport implements ToArray
{
    public static $field = [
        ['id' => 'name', 'name' => 'Name', 'required' => 'Yes'],
        ['id' => 'email', 'name' => 'Email', 'required' => 'Yes'],
        ['id' => 'value', 'name' => 'Lead Value', 'required' => 'No'],
        ['id' => 'note', 'name' => 'Note', 'required' => 'No'],
        ['id' => 'company_name', 'name' => 'Company Name', 'required' => 'No'],
        ['id' => 'company_website', 'name' => 'Official Website', 'required' => 'No'],
        ['id' => 'mobile', 'name' => 'Mobile', 'required' => 'No'],
        ['id' => 'company_phone', 'name' => 'Office Phone Number', 'required' => 'No'],
        ['id' => 'country', 'name' => 'Country', 'required' => 'No'],
        ['id' => 'state', 'name' => 'State', 'required' => 'No'],
        ['id' => 'city', 'name' => 'City', 'required' => 'No'],
        ['id' => 'postal_code', 'name' => 'Postal code', 'required' => 'No'],
        ['id' => 'address', 'name' => 'Company Address', 'required' => 'No'],
    ];

    public function array(array $array)
    {
        return $array;
    }

}
