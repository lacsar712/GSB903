<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  protected $table = 'users';
  protected $fillable = ['username', 'email', 'password_hash', 'role', 'is_active'];
  protected $hidden = ['password_hash'];
}
