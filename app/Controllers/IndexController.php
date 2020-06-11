<?php

namespace App\Controllers;

// use App\Models\{Job, Project};
use App\Utils\Response;


class IndexController extends BaseController {
  public function indexAction() {
    return Response::success(null, "Hola mundo", 201);
  }
}