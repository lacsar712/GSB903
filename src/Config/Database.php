<?php

namespace App\Config;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
  public static function init()
  {
    $capsule = new Capsule;

    $capsule->addConnection([
      'driver' => 'mysql',
      'host' => getenv('MYSQL_HOST') ?: '127.0.0.1', // 'db' in docker, 'localhost' in phpstudy
      'database' => 'sunny_coffee',
      'username' => getenv('MYSQL_USER') ?: 'root',
      'password' => getenv('MYSQL_PASSWORD') ?: 'root',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
    ]);

    // Make this Capsule instance available globally via static methods... (optional)
    $capsule->setAsGlobal();

    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
    $capsule->bootEloquent();
  }
}
