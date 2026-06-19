<?php
namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Utils\JWT;

class FavoriteController
{
  private function authenticate()
  {
    $token = JWT::getBearerToken();
    if (!$token) {
      jsonResponse(['error' => '请先登录'], 401);
    }
    $payload = JWT::decode($token);
    if (!$payload) {
      jsonResponse(['error' => '登录凭证已过期'], 401);
    }
    return $payload;
  }

  public function index()
  {
    $payload = $this->authenticate();
    $favorites = Favorite::with('product')
      ->where('user_id', $payload->sub)
      ->orderBy('created_at', 'desc')
      ->get();
    jsonResponse($favorites);
  }

  public function store()
  {
    $payload = $this->authenticate();
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['product_id'])) {
      jsonResponse(['error' => '缺少商品ID'], 400);
    }

    $product = Product::find($data['product_id']);
    if (!$product) {
      jsonResponse(['error' => '商品不存在'], 404);
    }

    $existing = Favorite::where('user_id', $payload->sub)
      ->where('product_id', $data['product_id'])
      ->first();

    if ($existing) {
      jsonResponse(['error' => '该商品已收藏'], 400);
    }

    $favorite = Favorite::create([
      'user_id' => $payload->sub,
      'product_id' => $data['product_id']
    ]);

    $favorite->load('product');
    jsonResponse($favorite, 201);
  }

  public function delete($id)
  {
    $payload = $this->authenticate();
    $favorite = Favorite::where('id', $id)
      ->where('user_id', $payload->sub)
      ->first();

    if (!$favorite) {
      jsonResponse(['error' => '收藏记录不存在'], 404);
    }

    $favorite->delete();
    jsonResponse(['message' => '已取消收藏']);
  }

  public function check($productId)
  {
    $payload = $this->authenticate();
    $favorite = Favorite::where('user_id', $payload->sub)
      ->where('product_id', $productId)
      ->first();
    jsonResponse(['is_favorited' => (bool)$favorite, 'favorite_id' => $favorite ? $favorite->id : null]);
  }
}
