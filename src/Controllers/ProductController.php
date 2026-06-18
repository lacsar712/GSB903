<?php
namespace App\Controllers;

use App\Models\Product;
use App\Utils\JWT;

class ProductController
{
  private function isAdmin()
  {
    $token = JWT::getBearerToken();
    if (!$token)
      return false;
    $payload = JWT::decode($token);
    return $payload && $payload->role === 'admin';
  }

  public function index()
  {
    $products = Product::all();
    jsonResponse($products);
  }

  public function store()
  {
    if (!$this->isAdmin()) {
      jsonResponse(['error' => '无权操作，请重新登录管理员账号'], 403);
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validation
    if (empty($data['name']) || empty($data['price']) || empty($data['category'])) {
      jsonResponse(['error' => '缺少必要字段（名称、价格或分类）'], 400);
    }
    
    if (!is_numeric($data['price']) || floatval($data['price']) <= 0) {
      jsonResponse(['error' => '商品价格必须为正数'], 400);
    }

    if (!in_array($data['category'], ['coffee', 'dessert'])) {
      jsonResponse(['error' => '是非法的商品分类'], 400);
    }

    if (isset($data['name']) && mb_strlen($data['name']) > 50) {
      jsonResponse(['error' => '产品名称不能超过50个字符'], 400);
    }

    $product = Product::create($data);
    jsonResponse($product, 201);
  }

  public function update($id)
  {
    if (!$this->isAdmin()) {
      jsonResponse(['error' => '无权操作，请重新登录管理员账号'], 403);
    }

    $product = Product::find($id);
    if (!$product) {
      jsonResponse(['error' => '商品不存在'], 404);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['name']) && strlen($data['name']) > 50) {
      jsonResponse(['error' => '产品名称不能超过50个字符'], 400);
    }

    $product->update($data);
    jsonResponse($product);
  }

  public function delete($id)
  {
    if (!$this->isAdmin()) {
      jsonResponse(['error' => '无权操作，请重新登录管理员账号'], 403);
    }

    $product = Product::find($id);
    if (!$product) {
      jsonResponse(['error' => '商品不存在'], 404);
    }

    $product->delete();
    jsonResponse(['message' => '商品已删除']);
  }
}
