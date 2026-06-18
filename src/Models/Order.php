<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $table = 'orders';
  protected $fillable = [
    'user_id', 
    'total_price', 
    'status',
    'remark'
  ];

  public $timestamps = true;

  public function items() {
      return $this->hasMany(OrderItem::class);
  }
}
