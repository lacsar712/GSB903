<?php
namespace App\Utils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
  private static function getKey() {
      return getenv('JWT_SECRET') ?: 'vibe_coding_secret_key';
  }

  public static function encode($payload)
  {
    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60 * 24); // 24 hours
    return FirebaseJWT::encode($payload, self::getKey(), 'HS256');
  }

  public static function decode($token)
  {
    try {
      return FirebaseJWT::decode($token, new Key(self::getKey(), 'HS256'));
    } catch (\Exception $e) {
      return null;
    }
  }

  public static function getBearerToken()
  {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
    $xAuthStr   = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
    
    if ($xAuthStr) {
        return $xAuthStr;
    }
    if (!$authHeader && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? null;
    }
    if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return $matches[1];
    }
    return null;
  }
}
