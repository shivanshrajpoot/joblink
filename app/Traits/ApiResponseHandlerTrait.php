<?php

namespace App\Traits;

trait ApiResponseHandlerTrait
{

    /**
     *  list of http error codes
     */
    public $HTTP_BAD_REQUEST = 400;
    public $HTTP_UNAUTHORIZED = 401;
    public $HTTP_FORBIDDEN = 403;
    public $HTTP_NOT_FOUND = 404;
    public $HTTP_INTERNAL_ERROR = 500;
    public $HTTP_INTERNAL_SERVICE_ERROR = 503;
    /**
     * Default status codes translation table.
     *
     * @var array
     */
    public $statusText = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Resource Not Found',
        500 => 'Some Technical Error Occurred',
        503 => 'Some Service Failed'
    ];
    /** default status code
     *
     * @var int
     */
    protected $statusCode = 200;
    /**
     * default per page limit
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Generates a Response with a 403 HTTP header and a given message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorForbidden($message = null)
    {
        $message = $message ? $message : $this->statusText[$this->HTTP_FORBIDDEN];

        return $this->respondWithErrors($message, $this->HTTP_FORBIDDEN);
    }

    /**
     * respond with array of errors
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithErrors($errors, $statusCode = 400)
    {
        return $this->setStatusCode($statusCode)->respond([
            'errors' => is_array($errors) ? $errors : $this->transformErrorToArray($errors)
        ]);
    }

    /**
     * @param $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param $error
     * @return array
     */
    public function transformErrorToArray($error)
    {
        return [['message' => $error]];
    }

    /**
     * Generates a Response with a 500 HTTP header and a given message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternalError($message = null)
    {
        $message = $message ? $message : $this->statusText[$this->HTTP_INTERNAL_ERROR];

        return $this->setStatusCode($this->HTTP_INTERNAL_ERROR)
            ->respondWithErrors($message);
    }


    /**
     * Generates a Response with a 500 HTTP header and a given message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternalServiceError($message = null)
    {
        $message = $message ? $message : $this->statusText[$this->HTTP_INTERNAL_SERVICE_ERROR];

        return $this->setStatusCode($this->HTTP_INTERNAL_SERVICE_ERROR)
            ->respondWithErrors($message);
    }

    /**
     * Generates a Response with a 404 HTTP header and a given message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotFound($message = null,$statusCode=400)
    {
        $message = $message ? $message : $this->statusText[$statusCode];

        return $this->setStatusCode($statusCode)
            ->respondWithErrors($message,$statusCode);
    }

    /**
     * Generates a Response with a 401 HTTP header and a given message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnauthorized($message = null)
    {
        $message = $message ? $message : $this->statusText[$this->HTTP_UNAUTHORIZED];

        return $this->setStatusCode($this->HTTP_UNAUTHORIZED)
            ->respondWithErrors($message);
    }

    /**
     * Generates a Response with a 400 HTTP header and a given message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorWrongArgs($message)
    {
        $message = $message ? $message : $this->statusText[$this->HTTP_BAD_REQUEST];

        return $this->setStatusCode($this->HTTP_BAD_REQUEST)
            ->respondWithErrors($message);
    }

    /**
     * transforms the validation errors
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transformValidationErrors($validation_exception)
    {
        $error_bag = [];

        $messages = $validation_exception->getErrors()->messages();

        foreach ($messages as $key => $message) {
            $error_bag[$key] = [
                'message' => $message[0]

            ];
        }

        return $error_bag;
    }

    /**
     * respond with array
     *
     * @param array $array
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithArray(array $array, array $headers = [])
    {
        $response = response()->json($array, $this->statusCode, $headers);

        $response->header('Content-Type', 'application/json');
        
        return $response;
    }

}