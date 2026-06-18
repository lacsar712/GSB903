<?php
namespace App\Controllers;

use App\Models\Message;
use App\Utils\JWT;

class MessageController
{
  private function getUser()
  {
    $token = JWT::getBearerToken();
    if (!$token)
      return null;
    return JWT::decode($token);
  }

  public function index()
  {
    $messages = Message::with('user:id,username,role')->orderBy('created_at', 'desc')->get();
    jsonResponse($messages);
  }

  public function store()
  {
    $user = $this->getUser();
    if (!$user) {
      jsonResponse(['error' => '请先登录'], 401);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['content'])) {
      jsonResponse(['error' => '留言内容不能为空'], 400);
    }

    $message = Message::create([
      'user_id' => $user->sub,
      'content' => $data['content']
    ]);

    $message->load('user:id,username,role');
    jsonResponse($message, 201);
  }
}
