<?php

namespace App\Middleware;
use App\Utils\Response;
use App\Utils\JWToken;

class AuthMiddleware {
  public static function isAuth($request) {
    $tokenHeader = $request->getHeader("authorization");
    $bearerToken = $tokenHeader && !empty($tokenHeader[0])? $tokenHeader[0] : '';

    $token = substr($bearerToken, 7);
    JWToken::verify($token);
  }
}
