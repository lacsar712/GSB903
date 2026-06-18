<?php
namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Utils\JWT;

class FavoriteController
{
  private function getUser()
  {
    $token = JWT::getBearerToken();
    if (!$token)
      return null;
    return JWT::decode($token);
  }

  private function requireAuth()
  {
    $user = $this->getUser();
    if (!$user) {
      jsonResponse(['error' => '请先登录'], 401);
      exit;
    }
    return $user;
  }

  public function index()
  {
    $user = $this->requireAuth();

    $favorites = Favorite::with('product')
      ->where('user_id', $user->sub)
      ->orderBy('created_at', 'desc')
      ->get();

    $result = $favorites->map(function ($fav) {
      return [
        'id' => $fav->id,
        'product_id' => $fav->product_id,
        'created_at' => $fav->created_at,
        'product' => $fav->product
      ];
    });

    jsonResponse($result->values());
  }

  public function toggle()
  {
    $user = $this->requireAuth();

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['product_id'])) {
      jsonResponse(['error' => '缺少商品ID'], 400);
    }

    $productId = $data['product_id'];

    $product = Product::find($productId);
    if (!$product) {
      jsonResponse(['error' => '商品不存在'], 404);
    }

    $existing = Favorite::where('user_id', $user->sub)
      ->where('product_id', $productId)
      ->first();

    if ($existing) {
      $existing->delete();
      jsonResponse([
        'message' => '已取消收藏',
        'is_favorited' => false,
        'product_id' => $productId
      ]);
    } else {
      $favorite = Favorite::create([
        'user_id' => $user->sub,
        'product_id' => $productId
      ]);
      $favorite->load('product');
      jsonResponse([
        'message' => '收藏成功',
        'is_favorited' => true,
        'favorite' => [
          'id' => $favorite->id,
          'product_id' => $favorite->product_id,
          'created_at' => $favorite->created_at,
          'product' => $favorite->product
        ]
      ], 201);
    }
  }

  public function check($productId)
  {
    $user = $this->getUser();
    if (!$user) {
      jsonResponse(['is_favorited' => false]);
    }

    $exists = Favorite::where('user_id', $user->sub)
      ->where('product_id', $productId)
      ->exists();

    jsonResponse(['is_favorited' => $exists]);
  }

  public function delete($id)
  {
    $user = $this->requireAuth();

    $favorite = Favorite::where('id', $id)
      ->where('user_id', $user->sub)
      ->first();

    if (!$favorite) {
      jsonResponse(['error' => '收藏记录不存在'], 404);
    }

    $favorite->delete();
    jsonResponse([
      'message' => '已取消收藏',
      'product_id' => $favorite->product_id
    ]);
  }
}
