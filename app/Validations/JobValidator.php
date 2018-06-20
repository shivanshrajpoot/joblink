<?php

namespace App\Validations;

/**
 * defines rules for form related to user
 *
 * Class JobValidator
 * @package App\Validations
 */
class JobValidator extends Validator
{
    /**
    * Custom messages for validation
    *
    * @param $type
    * @return array
    */
    public function messages($type)
    {
        $messages = [];

        switch($type) {
            default:
                break;
        }

        return $messages;
    }

    /**
    * validation rules
    *
    * @return array
    */
    protected function rules($type, $data)
    {
        $rules =  [];

        switch($type) {
            case 'create':
                $rules = [
                    'title'       => 'required|min:8|max:200',
                    'description' => 'required|min:25|max:500'
                ];
                break;
            case 'delete':
                $rules = ['id'    => 'required|exists:jobs'];
                break;
            case 'update':
                $rules = [
                    'id'          => 'required|exists:jobs',
                    'title'       => 'min:8|max:200',
                    'description' => 'min:25|max:500'
                ];
                break;
        }

        return $rules;
    }
}
