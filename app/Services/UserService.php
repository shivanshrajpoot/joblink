<?php

namespace App\Services;

use Hash;
use Mail;
use App\Models\User;
use App\Mail\UserRegistered;
use App\Mail\ResetPassword;
use App\Exceptions\LoginException;
use App\Validations\UserValidator;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class UserService {

    function __construct(UserValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
    *Registers User
    * 
    * @param  Array $inputs
    * @return App\Models\User $user
    */
    public function registerUser($inputs)
    {
        $this->validator->fire($inputs, 'register', []);

        $user = new User([
            'name' => array_get($inputs, 'name'),
            'email' => array_get($inputs, 'email'),
            'password' => array_get($inputs, 'password'),
            'type' => array_get($inputs, 'type'),
        ]);

        $user->password  = Hash::make(array_get($inputs, 'password'));

        $user->save();

        Mail::to($user)->send(new UserRegistered($user));

        return $user;
    }

    /**
    * Validates  user
    *
    * @param App\Models\User $user
    * @param Array $inputs
    * @return App\Models\User $user
    */
    public function validateAndGetUser($inputs)
    {
        $this->validator->fire($inputs, 'login', []);

        $valid = auth()->validate(
          array_only($inputs, ['email', 'password'])
        );

        if(!$valid)
        {
          throw new LoginException('Invalid Credentials');
        }
        return User::whereEmail(array_get($inputs, 'email'))->first();
    }

    /**
    * Updates user
    *
    * @param App\Models\User $user
    * @param Array $inputs
    * @return App\Models\User $user
    */
    public function update($user, $inputs)
    {
        $this->validator->fire($inputs, 'update', ['id' => $user->id]);

        $user->slug = null;

        $user->update($inputs);

        $user->save();

        return $user;
    }

    /**
    * Updates user password
    *
    * @param Array $inputs
    * @return App\Models\User $user
    */
    public function updateUserPassword($user, $inputs)
    {
        if($user) {

            $this->validator->fire($inputs, 'update-password', []);

            $user->password = Hash::make(array_get($inputs, 'password'));

            $user->save();

        }else{

            $this->validator->fire($inputs, 'reset-update-password', []);

            $user = User::whereEmail($inputs['email'])->first();

            if($user->otp == $inputs['otp']){
                
                $user->otp = NULL;

                $user->password = Hash::make(array_get($inputs, 'password'));

                $user->save();

            }else{
                
                throw new LoginException('Invalid OTP.');

            }

        }

        return $user;
    }

    /**
    * Generates OTP and Sends mail
    * @param  Array $inputs
    * @return  App\Models\User $user
    */
    public function resetUserPassword($inputs)
    {
        $this->validator->fire($inputs, 'forgot-password', []);

        $user = User::whereEmail(array_get($inputs, 'email'))->first();

        $user->otp = mt_rand(0000,9999);

        $user->save();

        Mail::to($user)->send(new ResetPassword($user));

        return $user;
    }

}
