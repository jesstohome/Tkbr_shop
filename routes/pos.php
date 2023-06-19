<?php

/*
|--------------------------------------------------------------------------
| POS Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\PosController;

Route::controller(PosController::class)->group(function () {
	Route::get('/pos/products', 'search')->name('pos.search_product');
	Route::post('/add-to-cart-pos', 'addToCart')->name('pos.addToCart');
	Route::post('/update-quantity-cart-pos', 'updateQuantity')->name('pos.updateQuantity');
	Route::post('/remove-from-cart-pos', 'removeFromCart')->name('pos.removeFromCart');
	Route::post('/get_shipping_address', 'getShippingAddress')->name('pos.getShippingAddress');
	Route::post('/get_shipping_address_seller', 'getShippingAddressForSeller')->name('pos.getShippingAddressForSeller');
	Route::post('/setDiscount', 'setDiscount')->name('pos.setDiscount');
	Route::post('/setShipping', 'setShipping')->name('pos.setShipping');
	Route::post('/set-shipping-address', 'set_shipping_address')->name('pos.set-shipping-address');
	Route::post('/pos-order-summary', 'get_order_summary')->name('pos.getOrderSummary');
	Route::post('/pos-order', 'order_store')->name('pos.order_place');
});

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
	//pos
	Route::controller(PosController::class)->group(function () {
		Route::get('/pos', 'index')->name('poin-of-sales.index');
		Route::get('/pos-activation', 'pos_activation')->name('poin-of-sales.activation');
		Route::get('/pos-conversation', 'pos_conversation')->name('poin-of-sales.conversation');
		Route::get('/pos-conversation-show/{id}', 'pos_conversation_show')->name('poin-of-sales.conversation-show');
        Route::post('pos-conversation/message/store', 'pos_conversation_message_store')->name('pos-conversation.message_store');
        Route::get('pos-conversation/message/destroy', 'message_destroy')->name('pos-conversation.message_destroy');
	});
});

//Seller
Route::group(['prefix' =>'seller', 'middleware' => ['seller', 'verified']], function(){
    //pos
	Route::get('/pos', [PosController::class, 'index'])->name('poin-of-sales.seller_index');
});


//salesman
Route::group(['prefix' =>'salesman', 'middleware' => ['auth', 'salesman']], function(){
    //pos
    Route::controller(PosController::class)->group(function () {
        Route::get('/pos', 'index')->name('salesman.poin-of-sales.index');
        Route::get('/pos-activation', 'pos_activation')->name('salesman.poin-of-sales.activation');
    });
});
