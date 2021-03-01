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
	],function(){

	    Route::post('register','AuthController@register');
	    Route::post('login', 'AuthController@login');
	    Route::post('logout', 'AuthController@logout');
	    Route::post('refresh', 'AuthController@refresh');
	    Route::post('me', 'AuthController@me');
	    Route::get('wishlist', 'UserController@wishlist');
	});


	//Products
	Route::group([ 
		'namespace' => 'Product',
		'prefix' => 'product'
	],function(){

	    Route::get('/all','ProductController@products');
	    Route::get('/categories','ProductController@categories');
	    Route::get('/sub-categories','ProductController@sub_categories');
	    Route::get('/view/single-product','ProductController@single_product');
	});

	//Blog
	Route::group([ 
		'namespace' => 'Blog',
		'prefix' => 'blog'
	],function(){

	    Route::get('/all','BlogController@blogs');
	});
   
});