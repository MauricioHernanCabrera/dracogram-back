<?php

namespace App\Middleware;
use App\Utils\Response;
use App\Utils\JWToken;

class AuthMiddleware {
  public static function isAuth($request) {
    $tokenHeader = $request->getHeader("Authorization");
    $bearerToken = $tokenHeader && !empty($tokenHeader[0])? $tokenHeader[0] : 'Bearer ';

    $token = substr($bearerToken, 7);
    JWToken::verify($token);
  }
}
