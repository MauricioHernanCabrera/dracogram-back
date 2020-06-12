<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Utils\Response;


class User extends Model {
  protected $table = 'users';
  protected $primaryKey = 'id';
  public $incrementing = true;

  public $timestamps = false;
  
  protected $fillable = ['email', 'firstName', 'lastName', 'password'];
  protected $hidden = ['password'];


  public static function existUser($user_id, $error = []) {
    $message = isset($error['message'])? $error['message'] : "¡No se encontro el usuario!";
    $status = isset($error['status'])? $error['status'] : 404;
    $user = User::find($user_id);
    if (!$user) Response::error($message, $status);
    return true;
  }

  public static function notExistUserByEmail($email, $error = []) {
    $message = !empty($error['message'])? $error['message'] : "¡El email ya esta tomado!";
    $status = !empty($error['status'])? $error['status'] : 400;
    $user = User::where('email', $email)->first();
    if ($user) Response::error($message, $status);
    return true;
  }

  public static function existUserByEmail($email, $error = []) {
    $message = !empty($error['message'])? $error['message'] : "¡No existe el usuario!";
    $status = !empty($error['status'])? $error['status'] : 400;
    $user = User::where('email', $email)->first();
    if (!$user) Response::error($message, $status);
    return true;
  }

  public function setPassword($password) {
    if (empty($password)) return;
    $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
  }

  public function comparePassword($password) {
    return password_verify($password, $this->attributes['password']);
  }
}