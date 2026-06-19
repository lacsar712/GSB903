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
    $userId = $payload->sub;

    $favorites = Favorite::with('product')
      ->where('user_id', $userId)
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

    jsonResponse($result->values()->all());
  }

  public function store()
  {
    $payload = $this->authenticate();
    $userId = $payload->sub;

    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['product_id'] ?? null;

    if (!$productId) {
      jsonResponse(['error' => '缺少商品ID'], 400);
    }

    $product = Product::find($productId);
    if (!$product) {
      jsonResponse(['error' => '商品不存在'], 404);
    }

    $existing = Favorite::where('user_id', $userId)
      ->where('product_id', $productId)
      ->first();

    if ($existing) {
      jsonResponse(['error' => '该商品已在收藏列表中'], 400);
    }

    $favorite = Favorite::create([
      'user_id' => $userId,
      'product_id' => $productId
    ]);

    $favorite->load('product');
    
    jsonResponse([
      'id' => $favorite->id,
      'product_id' => $favorite->product_id,
      'created_at' => $favorite->created_at,
      'product' => $favorite->product,
      'message' => '收藏成功'
    ], 201);
  }

  public function destroy($id)
  {
    $payload = $this->authenticate();
    $userId = $payload->sub;

    $favorite = Favorite::where('id', $id)
      ->where('user_id', $userId)
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
    $userId = $payload->sub;

    $favorite = Favorite::where('user_id', $userId)
      ->where('product_id', $productId)
      ->first();

    jsonResponse([
      'is_favorited' => $favorite ? true : false,
      'favorite_id' => $favorite ? $favorite->id : null
    ]);
  }
}
