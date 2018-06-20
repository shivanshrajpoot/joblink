<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\AccessException;
use Illuminate\Validation\ValidationException as BaseValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
      return $this->isAjaxCall($request) || $this->isApiCall($request) ?
         $this->renderJson($request, $exception) :
         $this->renderHtml($request, $exception);
    }

    public function renderHtml($request, $exception)
    {
      return parent::render($request, $exception);
    }

      /**
   * @param $request
   * @param $exception
   * @return \Illuminate\Http\JsonResponse
   */
  public function renderJson($request, $exception)
  {
    switch(true)
    {
      case $exception instanceof LoginException:
        $message = $exception->getMessage();
        return response()->error($message, 401);

      case $exception instanceof AccessException:
        $message = $exception->getMessage();
        return response()->error($message, 401);

      case $exception instanceof BaseValidationException:
        return response()->error([
          'message' => $exception->getMessage(),
          'validations' => $exception->errors()],
        422);

      case $exception instanceof NotFoundHttpException:
        return response()->error([
          'message' => $exception->getMessage(),
        ], $exception->getStatusCode());

      default:
        return response()->error($exception->getMessage(), 400);
    }
  }

     /**
   * is api call ?
   *
   * @param Request $request
   * @return bool
   */
  protected function isApiCall(Request $request)
  {
    return strpos($request->getUri(), '/api/v') !== false;
  }

  /**
   * is ajax call ?
   *
   * @param Request $request
   * @return bool
   */
  protected function isAjaxCall(Request $request)
  {
    return ($request->ajax() || $request->wantsJson()) ? true : false;
  }
}