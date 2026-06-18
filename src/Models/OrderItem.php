<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
  protected $table = 'order_items';
  protected $fillable = [
    'order_id', 
    'product_id', 
    'product_name_snapshot', 
    'price_snapshot', 
    'quantity'
  ];

  public $timestamps = false; // We don't have created_at and updated_at on this table

  public function order() {
      return $this->belongsTo(Order::class);
  }
}
