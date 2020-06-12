<?php

namespace App\Controllers;
use App\Utils\Response;
use App\Models\User;

class UserController extends BaseController {
  public function getAll($request) {
    $users = User::all();
    Response::success($users, "¡Se obtuvieron los usuarios!", 200);
  }

  public function createOne($request) {
    $data = json_decode($request->getBody());
    User::notExistUserByEmail($data->email);
    
    $payload = [
      'email' => $data->email,
      'password' => "xxxxxxxx",
      'firstName' => $data->firstName,
      'lastName' => $data->lastName,
    ];
    
    $user = User::create($payload);
    $user->setPassword($data->password);
    $user->save();

    Response::success($user, "¡Se creo el usuario!", 201);
  }
  
  public function getOne($request) {
    $id = $request->getAttribute('id');
    User::existUser($id);

    $user = User::find($id);
    Response::success($user, "¡Se creo el usuario!", 200);
  }

  public function updateOne($request) {
    $id = $request->getAttribute('id');
    User::existUser($id);

    $data = json_decode($request->getBody());
    $user = User::find($id);

    if ($data->email && $user->email != $data->email) {
      User::notExistUserByEmail($data->email);
      $user->email = $data->email;
    }
    if ($data->password) $user->setPassword($data->password);
    if ($data->firstName) $user->firstName = $data->firstName;
    if ($data->lastName) $user->lastName = $data->lastName;

    $user->save();

    Response::success($user, "¡Se actualizo el usuario!", 200);
  }

  public function deleteOne($request) {
    $id = $request->getAttribute('id');
    User::existUser($id);

    User::find($id)->delete();
    Response::success(null, "¡Se elimino el usuario!", 200);
  }
}