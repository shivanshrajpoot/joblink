<?php

namespace App\Validations;

use Illuminate\Validation\Factory;
use App\Exceptions\ValidationException;

abstract class Validator {

  /**
   * @var Factory
   */
  protected $validator;

  /**
   * @param Factory $validator
   */
  function __construct(Factory $validator)
  {
    $this->validator = $validator;
  }


  /**
   * Custom messages for validations
   *
   * @param $type
   * @return array
   */
  public function messages($type)
  {
    return [
      'image'  => 'Please select an image file (files ending with .jpg, .jpeg, .png extension)'
    ];
  }

  /**
   * fires validator
   *
   * @param $inputs
   * @return bool
   * @throws ValidationException
   */
  public function fire($inputs, $type, $data = [])
  {
    $validation = $this->validator->make($inputs, $this->rules($type, $data), $this->messages($type));

    $validation->setAttributeNames($this->getAttributeNamesForHuman($type));

    if($validation->fails()) throw new ValidationException($validation, $inputs);

    return true;
  }

  /**
   * Get the attributes name.
   *
   * @return array
   */
  protected function getAttributeNamesForHuman($type)
  {
    return [];
  }
}