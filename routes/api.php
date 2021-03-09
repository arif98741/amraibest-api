<?php

use Illuminate\Http\Request;

Route::group([

    'middleware' => 'api',
    'namespace' => 'Api',
    'prefix' => 'v1'

], function ($router) {

    //user management
    Route::group([
        'namespace' => 'User',
        'prefix' => 'user'
    ], function () {

        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('profile', 'AuthController@profile');
        Route::post('update-profile', 'UserController@update_profile');
        Route::post('wishlist', 'UserController@wishlist');
        Route::post('wishlist/store', 'UserController@store');
        Route::delete('wishlist/delete/{id}', 'UserController@delete');
        Route::post('orders', 'OrderController@orders');
        Route::post('order/single-order', 'OrderController@single_order');
        Route::post('order/order-tracks', 'OrderController@order_tracks');

    });

    //vendor management
    Route::group([
        'namespace' => 'Vendor',
        'prefix' => 'vendor'
    ], function () {

        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('profile', 'AuthController@profile');
        Route::post('update-profile', 'UserController@update_profile');
        Route::get('orders', 'OrderController@orders');
        Route::post('order/single-order', 'OrderController@single_order');
        Route::post('order/change-status', 'OrderController@change_status');
        Route::get('products', 'ProductController@datatables');
        Route::get('product/single-product', 'ProductController@single_product');
        Route::get('product/catelogs', 'ProductController@catalogdatatables');
        Route::post('product/delete', 'ProductController@destroy');
        Route::post('product/galleries', 'GalleryController@show_gallery_images');
        Route::post('product/galleries/store', 'GalleryController@store');
        Route::post('product/galleries/delete', 'GalleryController@destroy');
        Route::get('packages', 'PackageController@packageList');
        Route::post('package/store', 'PackageController@store');
        Route::post('package/update', 'PackageController@update');
        //Route::post('order/order-tracks', 'OrderController@order_tracks');

    });


    //Products
    Route::group([
        'namespace' => 'Product',
        'prefix' => 'product'
    ], function () {

        Route::get('/all', 'ProductController@products');
        Route::post('ratings', 'ProductController@ratings');
        Route::post('rating', 'ProductController@rating');
        Route::get('/categories', 'ProductController@categories');
        Route::get('/sub-categories', 'ProductController@sub_categories');
        Route::get('/sub-category/child-categories', 'ProductController@child_categories');
        Route::post('/view/single-product', 'ProductController@single_product');
    });

    //Blog
    Route::group([
        'namespace' => 'Blog',
        'prefix' => 'blog'
    ], function () {

        Route::get('/all', 'BlogController@blogs');
        Route::get('/all/by-category', 'BlogController@blogsByCategory');
        Route::get('/categories', 'BlogController@blogCategories');
        Route::get('view/single-blog', 'BlogController@single_blog');
        Route::post('store', 'BlogController@store');
        Route::post('update', 'BlogController@blogs');
    });

    //Blog
    Route::group([
        'namespace' => 'Extra',
        'prefix' => 'other'
    ], function () {
        Route::get('/countries', 'OtherController@countries');
        Route::get('/currencies', 'OtherController@currencies');
        Route::get('/coupons', 'OtherController@coupons');
    });


});