<?php
namespace App\Controllers;

use App\Models\User;
use App\Utils\JWT;

class AuthController
{
  public function login()
  {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (!$username || !$password) {
      jsonResponse(['error' => '需填写用户名和密码'], 400);
    }

    $user = User::where('username', $username)->first();

    if (!$user || !password_verify($password, $user->password_hash)) {
      jsonResponse(['error' => '用户名或密码错误'], 401);
    }

    if (!$user->is_active) {
      jsonResponse(['error' => '账号已被禁用'], 403);
    }

    $token = JWT::encode([
      'sub' => $user->id,
      'role' => $user->role,
      'username' => $user->username
    ]);

    jsonResponse([
      'token' => $token,
      'user' => [
        'id' => $user->id,
        'username' => $user->username,
        'role' => $user->role
      ]
    ]);
  }

  public function register()
  {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (!$username || !$password) {
      jsonResponse(['error' => '需填写用户名和密码'], 400);
    }

    if (strlen($username) < 3) {
      jsonResponse(['error' => '用户名长度不能少于3位'], 400);
    }

    if (strlen($password) < 6) {
      jsonResponse(['error' => '密码长度不能少于6位'], 400);
    }

    // Check if username already exists
    $existingUser = User::where('username', $username)->first();
    if ($existingUser) {
      jsonResponse(['error' => '用户名已存在'], 409);
    }

    // Create new user
    $user = User::create([
      'username' => $username,
      'password_hash' => password_hash($password, PASSWORD_DEFAULT),
      'role' => 'user',
      'is_active' => true
    ]);

    $token = JWT::encode([
      'sub' => $user->id,
      'role' => $user->role,
      'username' => $user->username
    ]);

    jsonResponse([
      'token' => $token,
      'user' => [
        'id' => $user->id,
        'username' => $user->username,
        'role' => $user->role
      ]
    ], 201);
  }
}
