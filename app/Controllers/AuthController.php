<?php

namespace App\Controllers;
use App\Utils\Response;
use App\Utils\JWToken;
use App\Models\User;

class AuthController extends BaseController {
  public function login ($request) {
    $data = json_decode($request->getBody());
    $errorResponse = ['status' => 401, 'message' => '¡Email o contraseña incorrecta!'];

    User::existUserByEmail($data->email, $errorResponse);

    $foundUser = User::where("email", $data->email)->first();
    $areEquals = $foundUser->comparePassword($data->password);

    if (!$areEquals) return Response::error($errorResponse['message'], $errorResponse['status']);

    $payload = [
      "email" => $foundUser->email,
      "firstName" => $foundUser->firstName,
      "lastName" => $foundUser->lastName,
    ];

    $token = JWToken::sign($payload);
    $user = JWToken::verify($token);

    Response::success(['token' => $token, 'user' => $user], "¡Usuario logueado!", 200);
  }

  public function verify ($request) {
    Response::success(null, "¡Token valido!", 200);
  }
}