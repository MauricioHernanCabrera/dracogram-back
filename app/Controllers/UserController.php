<?php

namespace App\Controllers;
use App\Utils\Response;

class UserController extends BaseController {
  public function getAll() {
    $users = [
      0 => [
        'email' => 'mauriciohernancabrera@gmail.com',
        'firstName' => 'Mauricio Hernan',
        'lastName' => 'Cabrera',
      ],
      1 => [
        'email' => 'mauriciohernancabrera2@gmail.com',
        'firstName' => 'Mauricio Hernan2',
        'lastName' => 'Cabrera2',
      ],
    ];

    Response::success($users, "¡Se obtuvieron los usuarios!", 200);
  }

  public function createOne($request) {
    $data = json_decode($request->getBody());
    Response::success($data, "¡Se creo el usuario!", 201);
  }
  
  public function getOne($request) {
    // dd(get_class_methods($request));
    $id = $request->getAttribute('id');
    
    $data = [
      'email' => 'mauriciohernancabrera2@gmail.com',
      'firstName' => 'Mauricio Hernan2',
      'lastName' => 'Cabrera2',
      'id' => $id,
    ];
    
    Response::success($data, "¡Se creo el usuario!", 200);
  }

  public function updateOne($request) {
    $id = $request->getAttribute('id');
    
    $data = [
      'email' => 'mauriciohernancabrera2@gmail.com',
      'firstName' => 'Mauricio Hernan2',
      'lastName' => 'Cabrera2',
      'id' => $id,
    ];
    
    Response::success($data, "¡Se actualizo el usuario!", 200);
  }

  public function deleteOne($request) {
    $id = $request->getAttribute('id');
    
    $data = [
      'email' => 'mauriciohernancabrera2@gmail.com',
      'firstName' => 'Mauricio Hernan2',
      'lastName' => 'Cabrera2',
      'id' => $id,
    ];
    
    Response::success($data, "¡Se elimino el usuario!", 200);
  }
}