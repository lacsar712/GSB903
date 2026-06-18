<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Utils\JWT;

class OrderController
{
  public function index()
  {
    $token = JWT::getBearerToken();
    if (!$token) {
      jsonResponse(['error' => '请先登录'], 401);
    }
    
    $payload = JWT::decode($token);
    if (!$payload) {
      jsonResponse(['error' => '登录凭证已过期'], 401);
    }

    if ($payload->role === 'admin') {
      $orders = Order::with('items')->orderBy('id', 'desc')->get();
    } else {
      $orders = Order::with('items')->where('user_id', $payload->sub)->orderBy('id', 'desc')->get();
    }
    
    jsonResponse($orders);
  }

  public function store()
  {
    $token = JWT::getBearerToken();
    if (!$token) {
      jsonResponse(['error' => '请先登录'], 401);
    }
    
    $payload = JWT::decode($token);
    if (!$payload) {
      jsonResponse(['error' => '登录凭证已过期'], 401);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $items = $data['items'] ?? [];
    $remark = $data['remark'] ?? '';

    if (empty($items) || !is_array($items)) {
      jsonResponse(['error' => '购物车为空'], 400);
    }

    $totalPrice = 0;
    $orderItemsData = [];

    // Verify all products in the cart securely
    foreach ($items as $item) {
        if (empty($item['product_id']) || empty($item['quantity'])) {
            jsonResponse(['error' => '商品数据格式不正确'], 400);
        }
        $product = Product::find($item['product_id']);
        if (!$product) {
            jsonResponse(['error' => '部分商品已下架或不存在'], 404);
        }
        
        $qty = intval($item['quantity']);
        if ($qty <= 0) {
            jsonResponse(['error' => '商品数量必须为正数'], 400);
        }

        $totalPrice += $product->price * $qty;
        
        $orderItemsData[] = [
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'price_snapshot' => $product->price,
            'quantity' => $qty
        ];
    }

    // Since we don't have DB transaction facade easily loaded here, do sequential inserts
    $order = Order::create([
        'user_id' => $payload->sub,
        'total_price' => $totalPrice,
        'remark' => $remark,
        'status' => 'pending'
    ]);

    foreach ($orderItemsData as $itemData) {
        $itemData['order_id'] = $order->id;
        OrderItem::create($itemData);
    }

    $order->load('items');
    jsonResponse($order, 201);
  }
}
