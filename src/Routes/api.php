<?php

// Auth
$router->post('/api/auth/login', 'App\\Controllers\\AuthController@login');
$router->post('/api/auth/register', 'App\\Controllers\\AuthController@register');
// $router->get('/api/auth/me', 'App\\Controllers\\AuthController@me'); // Middleware needed

// Products
$router->get('/api/products', 'App\\Controllers\\ProductController@index');
$router->post('/api/products', 'App\\Controllers\\ProductController@store'); // Admin only
$router->put('/api/products/(\\d+)', 'App\\Controllers\\ProductController@update'); // Admin only
$router->delete('/api/products/(\\d+)', 'App\\Controllers\\ProductController@delete'); // Admin only

// Messages
$router->get('/api/messages', 'App\\Controllers\\MessageController@index');
$router->post('/api/messages', 'App\\Controllers\\MessageController@store'); // Auth required

// Orders
$router->get('/api/orders', 'App\\Controllers\\OrderController@index'); // Auth required
$router->post('/api/orders', 'App\\Controllers\\OrderController@store'); // Auth required

// Favorites (Auth required)
$router->get('/api/favorites', 'App\\Controllers\\FavoriteController@index');
$router->post('/api/favorites/toggle', 'App\\Controllers\\FavoriteController@toggle');
$router->get('/api/favorites/check/(\d+)', 'App\\Controllers\\FavoriteController@check');
$router->delete('/api/favorites/(\d+)', 'App\\Controllers\\FavoriteController@delete');
