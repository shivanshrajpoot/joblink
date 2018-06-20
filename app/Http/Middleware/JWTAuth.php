<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JWTAuth extends GetUserFromToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, \Closure $next)
  {
    if (! $token = $this->auth->setRequest($request)->getToken())
    {
      return $this->respond('tymon.jwt.absent', 'Token not provided', 400);
    }

    try
    {
      $user = $this->auth->authenticate($token);
    }
    catch (TokenExpiredException $e)
    {
      return $this->respond('tymon.jwt.expired', 'Token Expired', $e->getStatusCode(), [$e]);
    }
    catch (JWTException $e)
    {
      return $this->respond('tymon.jwt.invalid', 'Token Invalid', $e->getStatusCode(), [$e]);
    }

    if (! $user)
    {
      return $this->respond('tymon.jwt.user_not_found', 'User not found !', 404);
    }

    $this->events->fire('tymon.jwt.valid', $user);

    return $next($request);
  }

  /**
   * respond
   *
   * @param  $event   
   * @param  $error   
   * @param  $status  
   * @param  $payload 
   * @return          
   */
  protected function respond($event, $error, $status, $payload = [])
  {
    $response = $this->events->fire($event, $payload, true);

    return $response ?: $this->response->json([
        'errors' => [
            [
              'message' => $error
            ]
        ]
    ], $status);
  }
}