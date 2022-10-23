<?php

namespace App\Http\Requests\Admin\App;

use App\Http\Requests\CoreRequest;

class UpdateAppSetting extends CoreRequest
{

    /** @return true  */
    public function authorize()
    {
        return true;
    }

    /** @return array  */
    public function rules()
    {
        $rules = [];
        $rules['allowed_file_types'] = 'required';

        if(!is_null($this->latitude)){
            $rules['latitude'] = 'required|numeric|gte:-90|lte:90';
        }

        if(!is_null($this->longitude)){
            $rules['longitude'] = 'required|numeric|gte:-180|lte:180';
        }

        return $rules;
    }

}
