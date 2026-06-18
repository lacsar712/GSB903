<?php
namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Utils\JWT;

class FavoriteController
{
  private function authUser()
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
    $payload = $this->authUser();

    $favorites = Favorite::with('product')
      ->where('user_id', $payload->sub)
      ->orderBy('id', 'desc')
      ->get();

    $list = [];
    foreach ($favorites as $fav) {
      if (!$fav->product) {
        continue;
      }
      $list[] = [
        'id'         => $fav->id,
        'product_id' => $fav->product_id,
        'created_at' => $fav->created_at,
        'product'    => $fav->product,
      ];
    }

    jsonResponse($list);
  }

  public function store()
  {
    $payload = $this->authUser();

    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['product_id'] ?? null;

    if (!$productId || !is_numeric($productId)) {
      jsonResponse(['error' => '商品ID不能为空'], 400);
    }

    $product = Product::find($productId);
    if (!$product) {
      jsonResponse(['error' => '商品不存在或已下架'], 404);
    }

    $existing = Favorite::where('user_id', $payload->sub)
      ->where('product_id', $productId)
      ->first();

    if ($existing) {
      jsonResponse(['error' => '该商品已在收藏列表中', 'favorite' => $existing], 409);
    }

    $favorite = Favorite::create([
      'user_id'    => $payload->sub,
      'product_id' => $productId,
    ]);

    $favorite->load('product');

    jsonResponse([
      'message'  => '收藏成功',
      'favorite' => $favorite,
    ], 201);
  }

  public function destroy($productId)
  {
    $payload = $this->authUser();

    $favorite = Favorite::where('user_id', $payload->sub)
      ->where('product_id', $productId)
      ->first();

    if (!$favorite) {
      jsonResponse(['error' => '收藏记录不存在'], 404);
    }

    $favorite->delete();

    jsonResponse(['message' => '已取消收藏', 'product_id' => intval($productId)]);
  }
}
