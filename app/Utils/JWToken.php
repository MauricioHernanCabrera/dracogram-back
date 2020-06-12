<?php
namespace App\Utils;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use App\Utils\Response;

class JWToken {
  public static function sign ($payload) {
    $secret = getenv('JWT_SECRET');
    $expirationDate = strtotime("+3 month", (int) time()) * 1000;
    $token = JWT::encode(array_merge($payload, ['exp' => $expirationDate]), $secret);
    return $token;
  }
  
  public static function verify ($token, $error = []) {
    $message = !empty($error['message'])? $error['message'] : "¡Token invalido!";
    $status = !empty($error['status'])? $error['status'] : 401;
    try {

      $secret = getenv('JWT_SECRET');
      $payload = JWT::decode($token, $secret, array('HS256'));

      $expirationDate = (int) time() * 1000;
      if ($expirationDate >= $payload->exp) return Response::error($message, $status);

      return $payload;
    } catch (SignatureInvalidException $error) {
      return Response::error($message, $status);
    }
  }
}

