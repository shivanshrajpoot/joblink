<?php

namespace App\Validations;

/**
 * defines rules for form related to user
 *
 * Class UserValidator
 * @package App\Validations
 */
class UserValidator extends Validator
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
            case 'forgot-password':
                $messages = ['Please provide valid email address.'];
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
            case 'register':
                $rules = [
                  'name'        => 'required',
                  'email'       => 'required|email|unique:users',
                  'password'    => 'required|min:6'
                ];
                break;

            case 'login':
                $rules = [
                  'email'       => 'required|email',
                  'password'    => 'required|min:6'
                ];
                break;

            case 'update':
                $rules = ['name' => 'required'];
                break;

            case 'forgot-password':
                $rules = ['email' => 'required|email|exists:users'];
                break;

            case 'reset-password':
                $rules = [
                    'token'     => 'required',
                    'email'     => 'required|email|exists:users',
                    'password'  => 'required|confirmed|min:6'
                ];
                break;

            case 'reset-update-password':
                $rules = [
                    'otp'       => 'required|min:4|max:4',
                    'email'     => 'required|email|exists:users',
                    'password'  => 'required|confirmed|min:6'
                ];
                break;
        }

        return $rules;
    }
}
