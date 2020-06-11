<?php
namespace App\Utils;


class Response {
  static function handle ($data = null, $message, $status, $error) {
    // http_response_code($status);
    
    $response = [
      'message' => $message,
      'data' => $data,
      'statusCode' => $status,
    ];

    http_response_code($status);
    echo json_encode($response);
    exit();
  }

  public static function success ($data, $message, $status) {
    self::handle($data, $message, $status, false);
  }

  public static function error ($message, $status) {
    self::handle(null, $message, $status, true);
  }
}

