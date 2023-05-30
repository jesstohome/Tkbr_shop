<?php

use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CommissionController;
use App\Models\AffiliateLog;
use App\Models\Currency;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\PaymentRecord;
use App\Models\ProductStock;
use App\Models\Address;
use App\Models\CustomerPackage;
use App\Models\Staff;
use App\Models\Ticket;
use App\Models\TicketHuaShu;
use App\Models\TicketHuaShuGroup;
use App\Models\TicketReply;
use App\Models\Upload;
use App\Models\Translation;
use App\Models\City;
use App\Utility\CategoryUtility;
use App\Models\Wallet;
use App\Models\CombinedOrder;
use App\Models\User;
use App\Models\Addon;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\Shop;
use App\Utility\SendSMSUtility;
use App\Utility\NotificationUtility;
use Carbon\Carbon;
use App\Models\CreditscoreStream;

//sensSMS function for OTP
if (!function_exists('sendSMS')) {
    function sendSMS($to, $from, $text, $template_id)
    {
        return SendSMSUtility::sendSMS($to, $from, $text, $template_id);
    }
}

//highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

//highlights the selected navigation on frontend
if (!function_exists('areActiveRoutesHome')) {
    function areActiveRoutesHome(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

//highlights the selected navigation on frontend
if (!function_exists('default_language')) {
    function default_language()
    {
        return env("DEFAULT_LANGUAGE");
    }
}

/**
 * Save JSON File
 * @return Response
 */
if (!function_exists('convert_to_usd')) {
    function convert_to_usd($amount)
    {
        $currency = Currency::find(get_setting('system_default_currency'));
        return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'USD')->first()->exchange_rate;
    }
}

if (!function_exists('convert_to_kes')) {
    function convert_to_kes($amount)
    {
        $currency = Currency::find(get_setting('system_default_currency'));
        return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'KES')->first()->exchange_rate;
    }
}

//filter products based on vendor activation system
if (!function_exists('filter_products')) {
    function filter_products($products, $front_page = 1)
    {
        if ($front_page) {
            $products = $products->where("added_by", '!=', 'admin');
        }
        $verified_sellers = verified_sellers_id();
        if (get_setting('vendor_system_activation') == 1) {
            return $products->where('approved', '1')->where('published', '1')->where('auction_product', 0)->orderBy('created_at', 'desc')->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            return $products->where('published', '1')->where('auction_product', 0)->where('added_by', 'admin');
        }
    }
}

//cache products based on category
if (!function_exists('get_cached_products')) {
    function get_cached_products($category_id = null)
    {
        $products = \App\Models\Product::where('published', 1)->where('approved', '1')->where('auction_product', 0)->where("added_by", '!=',  'admin')->orderByDesc('sort_value');
        $verified_sellers = verified_sellers_id();
        if (get_setting('vendor_system_activation') == 1) {
            $products = $products->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            $products = $products->where('added_by', 'admin');
        }

        if ($category_id != null) {
            return Cache::remember('products-category-' . $category_id, 86400, function () use ($category_id, $products) {
                $category_ids = CategoryUtility::children_ids($category_id);
                $category_ids[] = $category_id;
                return $products->whereIn('category_id', $category_ids)->latest()->take(12)->get();
            });
        } else {
            return Cache::remember('products', 86400, function () use ($products) {
                return $products->latest()->take(12)->get();
            });
        }
    }
}

if (!function_exists('verified_sellers_id')) {
    function verified_sellers_id()
    {
        return Cache::rememberForever('verified_sellers_id', function () {
            return App\Models\Shop::where('verification_status', 1)->pluck('user_id')->toArray();
        });
    }
}

if (!function_exists('get_system_default_currency')) {
    function get_system_default_currency()
    {
        return Cache::remember('system_default_currency', 86400, function () {
            return Currency::findOrFail(get_setting('system_default_currency'));
        });
    }
}

//converts currency to home default currency
if (!function_exists('convert_price')) {
    function convert_price($price)
    {
        if (Session::has('currency_code') && (Session::get('currency_code') != get_system_default_currency()->code)) {
            $price = floatval($price) / floatval(get_system_default_currency()->exchange_rate);
            $price = floatval($price) * floatval(Session::get('currency_exchange_rate'));
        }
        return $price;
    }
}

//gets currency symbol
if (!function_exists('currency_symbol')) {
    function currency_symbol()
    {
        if (Session::has('currency_symbol')) {
            return Session::get('currency_symbol');
        }
        return get_system_default_currency()->symbol;
    }
}

//formats currency
if (!function_exists('format_price')) {
    function format_price($price)
    {
        if (get_setting('decimal_separator') == 1) {
            $fomated_price = number_format($price, get_setting('no_of_decimals'));
        } else {
            $fomated_price = number_format($price, get_setting('no_of_decimals'), ',', '.');
        }

        if (get_setting('symbol_format') == 1) {
            return currency_symbol() . $fomated_price;
        } else if (get_setting('symbol_format') == 3) {
            return currency_symbol() . ' ' . $fomated_price;
        } else if (get_setting('symbol_format') == 4) {
            return $fomated_price . ' ' . currency_symbol();
        }
        return $fomated_price . currency_symbol();
    }
}

//formats price to home default price with convertion
if (!function_exists('single_price')) {
    function single_price($price)
    {
        return format_price(convert_price($price));
    }
}

if (!function_exists('discount_in_percentage')) {
    function discount_in_percentage($product)
    {
        $base = home_base_price($product, false);
        $reduced = home_discounted_base_price($product, false);
        $discount = $base - $reduced;
        $dp = ($discount * 100) / ($base > 0 ? $base : 1);
        return round($dp);
    }
}

//Shows Price on page based on carts
if (!function_exists('cart_product_price')) {
    function cart_product_price($cart_product, $product, $formatted = true, $tax = true)
    {
        $str = '';
        if (isset($cart_product['variation'])){
            if ($cart_product['variation'] != null) {
                $str = $cart_product['variation'];
            }
        }

        $price = (float)$product->unit_price;
        $product_stock = $product->stocks ? $product->stocks->where('variant', $str)->first() : '';
        if ($product_stock) {
            $price = $product_stock->price;
        }

        file_put_contents(storage_path('logs/order_store.log'), var_export([
            '$str' => $str,
            'unit_price' => $product->unit_price,
            'price' => $product_stock ? $product_stock->price : '',
            '$price' => $price,
        ], true), FILE_APPEND);


        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        //calculation of taxes
        if ($tax) {
            $taxAmount = 0;
            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $taxAmount += ($price * $product_tax->tax) / 100;
                } elseif ($product_tax->tax_type == 'amount') {
                    $taxAmount += $product_tax->tax;
                }
            }
            $price += $taxAmount;
        }

        if ($formatted) {
            return format_price(convert_price($price));
        } else {
            return $price;
        }

    }
}

if (!function_exists('cart_product_tax')) {
    function cart_product_tax($cart_product, $product, $formatted = true)
    {
        $str = '';
        if ($cart_product['variation'] != null) {
            $str = $cart_product['variation'];
        }
        $product_stock = $product->stocks->where('variant', $str)->first();
        $price = $product_stock->price;

        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        //calculation of taxes
        $tax = 0;
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        if ($formatted) {
            return format_price(convert_price($tax));
        } else {
            return $tax;
        }

    }
}

if (!function_exists('cart_product_discount')) {
    function cart_product_discount($cart_product, $product, $formatted = false)
    {
        $str = '';
        if ($cart_product['variation'] != null) {
            $str = $cart_product['variation'];
        }
        $product_stock = $product->stocks->where('variant', $str)->first();
        $price = $product_stock->price;

        //discount calculation
        $discount_applicable = false;
        $discount = 0;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $discount = ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $discount = $product->discount;
            }
        }

        if ($formatted) {
            return format_price(convert_price($discount));
        } else {
            return $discount;
        }

    }
}

// all discount
if (!function_exists('carts_product_discount')) {
    function carts_product_discount($cart_products, $formatted = false)
    {
        $discount = 0;
        foreach ($cart_products as $key => $cart_product) {
            $str = '';
            $product = \App\Models\Product::find($cart_product['product_id']);
            if ($cart_product['variation'] != null) {
                $str = $cart_product['variation'];
            }
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product_stock->price;

            //discount calculation
            $discount_applicable = false;

            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($product->discount_type == 'percent') {
                    $discount += ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $discount += $product->discount;
                }
            }
        }

        if ($formatted) {
            return format_price(convert_price($discount));
        } else {
            return $discount;
        }

    }
}

if (!function_exists('carts_coupon_discount')) {
    function carts_coupon_discount($code, $formatted = false)
    {
        $coupon = Coupon::where('code', $code)->first();
        $coupon_discount = 0;
        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);

                    $carts = Cart::where('user_id', Auth::user()->id)
                        ->where('owner_id', $coupon->user_id)
                        ->get();

                    if ($coupon->type == 'cart_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach ($carts as $key => $cartItem) {
                            $product = Product::find($cartItem['product_id']);
                            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                            $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                            $shipping += $cartItem['shipping_cost'];
                        }
                        $sum = $subtotal + $tax + $shipping;

                        if ($sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }

                        }
                    } elseif ($coupon->type == 'product_base') {
                        foreach ($carts as $key => $cartItem) {
                            $product = Product::find($cartItem['product_id']);
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['product_id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += (cart_product_price($cartItem, $product, false, false) * $coupon->discount / 100) * $cartItem['quantity'];
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount * $cartItem['quantity'];
                                    }
                                }
                            }
                        }
                    }

                }
            }

            if ($coupon_discount > 0) {
                Cart::where('user_id', Auth::user()->id)
                    ->where('owner_id', $coupon->user_id)
                    ->update(
                        [
                            'discount' => $coupon_discount / count($carts),
                        ]
                    );
            } else {
                Cart::where('user_id', Auth::user()->id)
                    ->where('owner_id', $coupon->user_id)
                    ->update(
                        [
                            'discount' => 0,
                            'coupon_code' => null,
                        ]
                    );
            }
        }

        if ($formatted) {
            return format_price(convert_price($coupon_discount));
        } else {
            return $coupon_discount;
        }
    }
}

//Shows Price on page based on low to high
if (!function_exists('home_price')) {
    function home_price($product, $formatted = true)
    {
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $lowest_price += ($lowest_price * $product_tax->tax) / 100;
                $highest_price += ($highest_price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $lowest_price += $product_tax->tax;
                $highest_price += $product_tax->tax;
            }
        }

        if ($formatted) {
            if ($lowest_price == $highest_price) {
                return format_price(convert_price($lowest_price));
            } else {
                return format_price(convert_price($lowest_price)) . ' - ' . format_price(convert_price($highest_price));
            }
        } else {
            return $lowest_price . ' - ' . $highest_price;
        }
    }
}

//Shows Price on page based on low to high with discount
if (!function_exists('home_discounted_price')) {
    function home_discounted_price($product, $formatted = true)
    {
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->discount) / 100;
                $highest_price -= ($highest_price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $lowest_price -= $product->discount;
                $highest_price -= $product->discount;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $lowest_price += ($lowest_price * $product_tax->tax) / 100;
                $highest_price += ($highest_price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $lowest_price += $product_tax->tax;
                $highest_price += $product_tax->tax;
            }
        }

        if ($formatted) {
            if ($lowest_price == $highest_price) {
                return format_price(convert_price($lowest_price));
            } else {
                return format_price(convert_price($lowest_price)) . ' - ' . format_price(convert_price($highest_price));
            }
        } else {
            return $lowest_price . ' - ' . $highest_price;
        }
    }
}

//Shows Base Price
if (!function_exists('home_base_price_by_stock_id')) {
    function home_base_price_by_stock_id($id)
    {
        $product_stock = ProductStock::findOrFail($id);
        $price = $product_stock->price;
        $tax = 0;

        foreach ($product_stock->product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;
        return format_price(convert_price($price));
    }
}
if (!function_exists('home_base_price')) {
    function home_base_price($product, $formatted = true)
    {
        $price = $product->unit_price;
        $tax = 0;

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;
        return $formatted ? format_price(convert_price($price)) : $price;
    }
}

//Shows Base Price with discount
if (!function_exists('home_discounted_base_price_by_stock_id')) {
    function home_discounted_base_price_by_stock_id($id)
    {
        $product_stock = ProductStock::findOrFail($id);
        $product = $product_stock->product;
        $price = $product_stock->price;
        $tax = 0;

        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;

        return format_price(convert_price($price));
    }
}

//Shows Base Price with discount
if (!function_exists('home_discounted_base_price')) {
    function home_discounted_base_price($product, $formatted = true)
    {
        $price = $product->unit_price;
        $tax = 0;

        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        $price += $tax;

        return $formatted ? format_price(convert_price($price)) : $price;
    }
}

if (!function_exists('renderStarRating')) {
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i class = 'las la-star active'></i>";
        $halfStar = "<i class = 'las la-star half'></i>";
        $emptyStar = "<i class = 'las la-star'></i>";
        $rating = $rating <= $maxRating ? $rating : $maxRating;

        $fullStarCount = (int)$rating;
        $halfStarCount = ceil($rating) - $fullStarCount;
        $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);
        echo $html;
    }
}

function translate($key, $lang = null, $addslashes = false)
{
    if ($lang == null) {
        $lang = App::getLocale();
    }
    $lang_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($key)));

    $translations_en = Cache::rememberForever('translations-en', function () {
        return Translation::where('lang', 'en')->pluck('lang_value', 'lang_key')->toArray();
    });

    if (!isset($translations_en[$lang_key])) {
        $translation_def = new Translation;
        $translation_def->lang = 'en';
        $translation_def->lang_key = $lang_key;
        $translation_def->lang_value = str_replace(array("\r", "\n", "\r\n"), "", $key);
        $translation_def->save();
        Cache::forget('translations-en');
    }

    // return user session lang
    $translation_locale = Cache::rememberForever("translations-{$lang}", function () use ($lang) {
        return Translation::where('lang', $lang)->pluck('lang_value', 'lang_key')->toArray();
    });
    if (isset($translation_locale[$lang_key])) {
        return $addslashes ? addslashes(trim($translation_locale[$lang_key])) : trim($translation_locale[$lang_key]);
    }

    // return default lang if session lang not found
    $translations_default = Cache::rememberForever('translations-' . env('DEFAULT_LANGUAGE', 'en'), function () {
        return Translation::where('lang', env('DEFAULT_LANGUAGE', 'en'))->pluck('lang_value', 'lang_key')->toArray();
    });
    if (isset($translations_default[$lang_key])) {
        return $addslashes ? addslashes(trim($translations_default[$lang_key])) : trim($translations_default[$lang_key]);
    }

    // fallback to en lang
    if (!isset($translations_en[$lang_key])) {
        return trim($key);
    }
    return $addslashes ? addslashes(trim($translations_en[$lang_key])) : trim($translations_en[$lang_key]);
}

function remove_invalid_charcaters($str)
{
    $str = str_ireplace(array("\\"), '', $str);
    return str_ireplace(array('"'), '\"', $str);
}

function getShippingCost($carts, $index)
{
    $admin_products = array();
    $seller_products = array();

    $cartItem = $carts[$index];
    $product = Product::find($cartItem['product_id']);

    if ($product->digital == 1) {
        return 0;
    }

    foreach ($carts as $key => $cart_item) {
        $item_product = Product::find($cart_item['product_id']);
        if ($item_product->added_by == 'admin') {
            array_push($admin_products, $cart_item['product_id']);
        } else {
            $product_ids = array();
            if (isset($seller_products[$item_product->user_id])) {
                $product_ids = $seller_products[$item_product->user_id];
            }
            array_push($product_ids, $cart_item['product_id']);
            $seller_products[$item_product->user_id] = $product_ids;
        }
    }

    if (get_setting('shipping_type') == 'flat_rate') {
        return get_setting('flat_rate_shipping_cost') / count($carts);
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if ($product->added_by == 'admin') {
            return get_setting('shipping_cost_admin') / count($admin_products);
        } else {
            return Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();
        $city = City::where('id', $shipping_info->city_id)->first();
        if ($city != null) {
            if ($product->added_by == 'admin') {
                return $city->cost / count($admin_products);
            } else {
                return $city->cost / count($seller_products[$product->user_id]);
            }
        }
        return 0;
    } else {
        if ($product->is_quantity_multiplied && get_setting('shipping_type') == 'product_wise_shipping') {
            return $product->shipping_cost * $cartItem['quantity'];
        }
        return $product->shipping_cost;
    }
}

function timezones()
{
    return Timezones::timezonesToArray();
}

if (!function_exists('app_timezone')) {
    function app_timezone()
    {
        return config('app.timezone');
    }
}

//return file uploaded via uploader
if (!function_exists('uploaded_asset')) {
    function uploaded_asset($id)
    {
        if (($asset = \App\Models\Upload::find($id)) != null) {
            return $asset->external_link == null ? my_asset($asset->file_name) : $asset->external_link;
        }
        return null;
    }
}

if (!function_exists('my_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function my_asset($path, $secure = null)
    {
        if( strpos($path,'http') !== false  ) return $path;
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return Storage::disk('s3')->url($path);
        } else {
            return app('url')->asset('public/' . $path, $secure);
        }
    }
}

if (!function_exists('static_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function static_asset($path, $secure = null)
    {
        return app('url')->asset('public/' . $path, $secure);
    }
}


// if (!function_exists('isHttps')) {
//     function isHttps()
//     {
//         return !empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS']);
//     }
// }

if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = '//' . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}


if (!function_exists('getFileBaseURL')) {
    function getFileBaseURL()
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return env('AWS_URL') . '/';
        } else {
            return getBaseURL() . 'public/';
        }
    }
}


if (!function_exists('isUnique')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function isUnique($email)
    {
        $user = \App\Models\User::where('email', $email)->first();

        if ($user == null) {
            return '1'; // $user = null means we did not get any match with the email provided by the user inside the database
        } else {
            return '0';
        }
    }
}

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null, $lang = false)
    {
        $settings = Cache::remember('business_settings', 86400, function () {
            return BusinessSetting::all();
        });

        if ($lang == false) {
            $setting = $settings->where('type', $key)->first();
        } else {
            $setting = $settings->where('type', $key)->where('lang', $lang)->first();
            $setting = !$setting ? $settings->where('type', $key)->first() : $setting;
        }
        return $setting == null ? $default : $setting->value;
    }
}

if (!function_exists('get_admin_setting')) {
    function get_admin_setting($key, $default = null, $lang = false)
    {
        $admin_id = Auth::user()->id;
        $settings = Cache::remember('business_admin_settings', 86400, function () use($admin_id) {
            return BusinessSetting::where('admin_id', $admin_id)->get();
        });

        if ($lang == false) {
            $setting = $settings->where('admin_id', $admin_id)->where('type', $key)->first();
        } else {
            $setting = $settings->where('admin_id', $admin_id)->where('type', $key)->where('lang', $lang)->first();
            $setting = !$setting ? $settings->where('admin_id', $admin_id)->where('type', $key)->first() : $setting;
        }
        return $setting == null ? $default : $setting->value;
    }
}

function hex2rgba($color, $opacity = false)
{
    return false;
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        if (Auth::check() && (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('isSeller')) {
    function isSeller()
    {
        if (Auth::check() && Auth::user()->user_type == 'seller') {
            return true;
        }
        return false;
    }
}

if (!function_exists('isCustomer')) {
    function isCustomer()
    {
        if (Auth::check() && Auth::user()->user_type == 'customer') {
            return true;
        }
        return false;
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

// duplicates m$ excel's ceiling function
if (!function_exists('ceiling')) {
    function ceiling($number, $significance = 1)
    {
        return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
    }
}

//for api
if (!function_exists('get_images_path')) {
    function get_images_path($given_ids, $with_trashed = false)
    {
        $paths = [];
        foreach (explode(',', $given_ids) as $id) {
            $paths[] = uploaded_asset($id);
        }

        return $paths;
    }
}

//for api
if (!function_exists('checkout_done')) {
    function checkout_done($combined_order_id, $payment)
    {
        $combined_order = CombinedOrder::find($combined_order_id);

        foreach ($combined_order->orders as $key => $order) {
            $order->payment_status = 'paid';
            $order->payment_details = $payment;
            $order->save();

            try {
                NotificationUtility::sendOrderPlacedNotification($order);
                calculateCommissionAffilationClubPoint($order);
            } catch (\Exception $e) {
            }
        }
    }
}

//for api
if (!function_exists('wallet_payment_done')) {
    function wallet_payment_done($user_id, $amount, $payment_method, $payment_details)
    {
        $user = \App\Models\User::find($user_id);
        $user->balance = $user->balance + $amount;
        $user->save();

        $wallet = new Wallet;
        $wallet->user_id = $user->id;
        $wallet->amount = $amount;
        $wallet->payment_method = $payment_method;
        $wallet->payment_details = $payment_details;
        $wallet->save();
    }
}

if (!function_exists('purchase_payment_done')) {
    function purchase_payment_done($user_id, $package_id)
    {
        $user = User::findOrFail($user_id);
        $user->customer_package_id = $package_id;
        $customer_package = CustomerPackage::findOrFail($package_id);
        $user->remaining_uploads += $customer_package->product_upload;
        $user->save();

        return 'success';
    }
}

if (!function_exists('storehouseProduct_payment_done')) {
    function storehouseProduct_payment_done($order_id, $payment_code, $third_order_code = '')
    {
        $order = Order::findOrFail($order_id);
        $shop = $order->shop;
        // 累计冻结资金
        $shop->admin_to_pay += $order->grand_total;
        $shop->save();

        // 保存订单冻结资金过期时间
        $freezeDays = get_setting('frozen_funds_unfrozen_days', 15);
        $order->freeze_expired_at = \Illuminate\Support\Carbon::now()->addDays($freezeDays)->timestamp;

        $order->pickup_time = time();
        $order->product_storehouse_status = 1;

        // 按自动物流配置生成物流信息
        $express['express_name'] = 'FedEx';
        $express['express_code'] = gen_rand_no(12);
        $express['express_info'] = [
            'Order created successfully, stocking in progress',
            'The express has been loaded in the warehouse and is ready to be sent to the distribution center',
            'The courier has arrived at the distribution center and is currently sorting',
            'The express has been loaded and is ready to be sent to the distribution center',
            'The courier has arrived at the delivery center',
            'The courier is delivering Please keep in touch normally and have turned on \'secure call\' to protect your phone privacy. Please answer with confidence',
            'The express  has been received. Thank you for using FedEx and we look forward to serving you again',
        ];
        $express['express_time'] = [];
        $logistics_times = json_decode(get_setting('logistics_times'), true);
        foreach (array_chunk($logistics_times, 2) as $time_value) {
            $express['express_time'][] = date('Y:m:d H:i:s', time() + mt_rand(...$time_value));
        }
        $order->express_info = json_encode($express);

        $order->save();

        // 保存提货付款记录
        $record = new PaymentRecord();
        $record->order_id = $order->id;
        $record->bloc_id = $shop->bloc_id;
        $record->staff_id = $shop->staff_id;
        $record->seller_id = $order->seller_id;
        $record->buyer_id = $order->user_id;
        $record->amount = $order->product_storehouse_total;
        $record->pay_status = 1;
        $record->payment_code = $payment_code;
        $record->pay_time = time();
        $record->out_order_no = $third_order_code;
        $record->save();

        hset_plus('orders_pick_up_tip', $order_id, 1, $order->staff_id);

        return true;
    }
}

if (!function_exists('product_restock')) {
    function product_restock($orderDetail)
    {
        $variant = $orderDetail->variation;
        if ($orderDetail->variation == null) {
            $variant = '';
        }

        $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
            ->where('variant', $variant)
            ->first();

        if ($product_stock != null) {
            $product_stock->qty += $orderDetail->quantity;
            $product_stock->save();
        }
    }
}

//Commission Calculation
if (!function_exists('calculateCommissionAffilationClubPoint')) {
    function calculateCommissionAffilationClubPoint($order)
    {
        (new CommissionController)->calculateCommission($order);

        if (addon_is_activated('affiliate_system')) {
            (new AffiliateController)->processAffiliatePoints($order);
        }

        if (addon_is_activated('club_point')) {
            if ($order->user != null) {
                (new ClubPointController)->processClubPoints($order);
            }
        }

        $order->commission_calculated = 1;
        $order->save();
    }
}

// product storehouse order free up
if (!function_exists('product_storehouse_order_free_up')) {
    function product_storehouse_order_free_up($orderId)
    {
        $order = Order::query()->find($orderId);
        if (!$order) return false;
        try {
            DB::beginTransaction();

            // 判断是否已经释放
            if (!$order->freeze_expired_at) return false;

            $shop = $order->shop;
            $user = $shop->user;
            if (!$shop) return false;

            $grand_total = $order->grand_total;

            if ($order->picking_switch!=1){
                $grand_total = $order->grand_total - $order->product_storehouse_total;
            }


            $shop->admin_to_pay -= $grand_total; // 减少冻结资金
            $user->balance += $grand_total; // 增加用户钱包余额
            $order->freeze_expired_at = null; // 标记订单已经释放
            $order->unfreeze_time = time(); // 标记订单释放时间点

            //结算商家
            $seller_1 = User::where('id', $user->pid)->first();
            if ($seller_1){
                $seller_1->balance += $grand_total*get_setting('commission_ratio_level_1')/100;
                $seller_1->save();
                //写收入日志
                $affiliate_log = new AffiliateLog;
                $affiliate_log->user_id = $user->id;
                $affiliate_log->referred_by_user = $seller_1->id;
                $affiliate_log->amount = $grand_total*get_setting('commission_ratio_level_1')/100;
                $affiliate_log->order_id = $order->id;
                $affiliate_log->affiliate_type = '';
                $affiliate_log->save();

                $seller_2 = User::where('id', $seller_1->pid)->first();
                if ($seller_2){
                    $seller_2->balance += $grand_total*get_setting('commission_ratio_level_2')/100;
                    $seller_2->save();
                    //写收入日志
                    $affiliate_log = new AffiliateLog;
                    $affiliate_log->user_id = $user->id;
                    $affiliate_log->referred_by_user = $seller_2->id;
                    $affiliate_log->amount = $grand_total*get_setting('commission_ratio_level_2')/100;
                    $affiliate_log->order_id = $order->id;
                    $affiliate_log->affiliate_type = '';
                    $affiliate_log->save();
                    $seller_3 = User::where('id', $seller_2->pid)->first();
                    if ($seller_3){
                        $seller_3->balance += $grand_total*get_setting('commission_ratio_level_3')/100;
                        $seller_3->save();
                        //写收入日志
                        $affiliate_log = new AffiliateLog;
                        $affiliate_log->user_id = $user->id;
                        $affiliate_log->referred_by_user = $seller_3->id;
                        $affiliate_log->amount = $grand_total*get_setting('commission_ratio_level_3')/100;
                        $affiliate_log->order_id = $order->id;
                        $affiliate_log->affiliate_type = '';
                        $affiliate_log->save();
                    }
                }
            }

            //结算商家 结束

            //结算用户
            $user_1 = User::where('id', $order->user->pid)->first();
            if ($user_1){
                $user_1->balance += $grand_total*get_setting('commission_ratio_level_1')/100;
                $user_1->save();
                //写收入日志
                $affiliate_log = new AffiliateLog;
                $affiliate_log->user_id = $order->user_id;
                $affiliate_log->referred_by_user = $user_1->id;
                $affiliate_log->amount = $grand_total*get_setting('commission_ratio_level_1')/100;
                $affiliate_log->order_id = $order->id;
                $affiliate_log->affiliate_type = '';
                $affiliate_log->save();

                $user_2 = User::where('id', $user_1->pid)->first();
                if ($user_2){
                    $user_2->balance += $grand_total*get_setting('commission_ratio_level_2')/100;
                    $user_2->save();
                    //写收入日志
                    $affiliate_log = new AffiliateLog;
                    $affiliate_log->user_id = $order->user_id;
                    $affiliate_log->referred_by_user = $user_2->id;
                    $affiliate_log->amount = $grand_total*get_setting('commission_ratio_level_2')/100;
                    $affiliate_log->order_id = $order->id;
                    $affiliate_log->affiliate_type = '';
                    $affiliate_log->save();

                    $user_3 = User::where('id', $user_2->pid)->first();
                    if ($user_3){
                        $user_3->balance += $grand_total*get_setting('commission_ratio_level_3')/100;
                        $user_3->save();
                        //写收入日志
                        $affiliate_log = new AffiliateLog;
                        $affiliate_log->user_id = $order->user_id;
                        $affiliate_log->referred_by_user = $user_3->id;
                        $affiliate_log->amount = $grand_total*get_setting('commission_ratio_level_3')/100;
                        $affiliate_log->order_id = $order->id;
                        $affiliate_log->affiliate_type = '';
                        $affiliate_log->save();
                    }
                }
            }

            $shop->save();
            $user->save();
            $order->save();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }
}

if (!function_exists('timedquery')) {
    /**
     * 定时记录未按时提货的订单
     */
    function timedquery($orderId){

        $orders = Order::query()->find($orderId);
        if (!$orders) return false;
        // 判断是否已经支付
        if ($orders->product_storehouse_status == 1) return false;

        //循环扣信用分
        $creditscorestream = CreditscoreStream::where('order_id','=',$orders["id"] )->first();
            if ($creditscorestream == null)
            {
                $users = User::where('id','=',$orders['seller_id'])->first();
                $settimeone = strtotime($orders['created_at'].'+6 hours');
                if ($orders['order_type'] == 6 && date('Y-m-d h:i:s',$settimeone) < date('Y-m-d H:i:s'))
                {
                    //增加明细
                    $creditscore_stream = new CreditscoreStream;
                    $creditscore_stream['order_id'] = $orders['id'];
                    $creditscore_stream['seller_id'] = $orders['seller_id'];
                    $creditscore_stream['creditscore_before'] = $users['creditscore'];
                    $creditscore_stream['creditscore_after'] = $users['creditscore'] - 2;
                    $creditscore_stream['remark'] = '';
                    $creditscore_stream->save();
                }
                $settimetwo = strtotime($orders['created_at'].'+24 hours');
                if ($orders['order_type'] == 24 && date('Y-m-d h:i:s',$settimetwo) < date('Y-m-d H:i:s'))
                {
                    //增加明细
                    $creditscore_stream = new CreditscoreStream;
                    $creditscore_stream['order_id'] = $orders['id'];
                    $creditscore_stream['seller_id'] = $orders['seller_id'];
                    $creditscore_stream['creditscore_before'] = $users['creditscore'];
                    $creditscore_stream['creditscore_after'] = $users['creditscore'] - 2;
                    $creditscore_stream['remark'] = '';
                    $creditscore_stream->save();
                }
                $settimethree = strtotime($orders['created_at'].'+24 hours');
                if ($orders['order_type'] == 24 && date('Y-m-d h:i:s',$settimethree) < date('Y-m-d H:i:s'))
                {
                    //增加明细
                    $creditscore_stream = new CreditscoreStream;
                    $creditscore_stream['order_id'] = $orders['id'];
                    $creditscore_stream['seller_id'] = $orders['seller_id'];
                    $creditscore_stream['creditscore_before'] = $users['creditscore'];
                    $creditscore_stream['creditscore_after'] = $users['creditscore'] - 2;
                    $creditscore_stream['remark'] = '';
                    $creditscore_stream->save();
                }

                $users['creditscore'] = $users['creditscore'] - 2;
                $users -> save();

            }
    }
}

if (!function_exists('get_express_status')) {
    function get_express_status() {
        $keys = [
            'prepare_goods',
            'sent_to_the_distribution_center',
            'distribution_sorting',
            'sent_to_the_delivery_center',
            'delivery_sorting',
            'delivery_in_progress',
            'delivered',
        ];
        $status = [];
        foreach ($keys as $status_key) {
            $status[$status_key] = $status_key == 'delivered' ? translate('Received') : translate(str_replace("_", " ", $status_key));
        }

        return $status;
    }
}

// 按物流时间，定时更新发货状态
if (!function_exists('scheduled_update_delivery_status')) {
    function scheduled_update_delivery_status($order) {
        if (empty($order) || empty($order->express_info)) return;

        $now = time();
        $status = array_keys(get_express_status());
        $express = json_decode($order->express_info, true);
        if (!empty($express['express_time'])) {
            $delivery_status = '';
            foreach ($express['express_time'] as $key => $time) {
                $time = strtotime(is_array($time) ? $time[0] : $time);
                if ($now >= $time && isset($status[$key])) {
                    $delivery_status = $status[$key];
                }
            }

            echo $order->id, ' ', $delivery_status, ' ', $order->delivery_status . PHP_EOL;
            if (!empty($delivery_status) && $order->delivery_status != $delivery_status) {
                $order->delivery_status = $delivery_status;
                // 到已签收状态时，重置订单的冻结时间
                if ($delivery_status == 'received') {
                    $freezeDays = get_setting('frozen_funds_unfrozen_days', 15);
                    $order->freeze_expired_at = \Illuminate\Support\Carbon::now()->addDays($freezeDays)->timestamp;
                }
                $order->save();
            }
        }
    }
}

// Addon Activation Check
if (!function_exists('addon_is_activated')) {
    function addon_is_activated($identifier, $default = null)
    {
        $addons = Cache::remember('addons', 86400, function () {
            return Addon::all();
        });

        $activation = $addons->where('unique_identifier', $identifier)->where('activated', 1)->first();
        return $activation == null ? false : true;
    }
}

// Addon Activation Check
if (!function_exists('seller_package_validity_check')) {
    function seller_package_validity_check($user_id = null)
    {
        $user = $user_id == null ? \App\Models\User::find(Auth::user()->id) : \App\Models\User::find($user_id);
        $shop = $user->shop;
        $package = \App\Models\SellerPackage::query()->where('is_default', 1)->first();
        $package_validation = false;
        if (
            $package->product_upload_limit > $shop->user->products()->count()
        ) {
            $package_validation = true;
        }

        return $package_validation;
        // Ture = Seller package is valid and seller has the product upload limit
        // False = Seller package is invalid or seller product upload limit exists.
    }
}

// Get URL params
if (!function_exists('get_url_params')) {
    function get_url_params($url, $key)
    {
        $query_str = parse_url($url, PHP_URL_QUERY);
        parse_str($query_str, $query_params);

        return $query_params[$key] ?? '';
    }
}

// 生成指定长度的字符数字串
if (!function_exists('gen_rand_no')) {
    function gen_rand_no($len)
    {
        $pow = pow(10, $len - 1);
        return mt_rand(1 * $pow, 9 * $pow);
    }
}

// 数据分离-过滤条件
if (!function_exists('filter_by_bloc')) {
    function filter_by_bloc($model) {
        if (\Auth::user()->user_type != 'admin') {
            // 按集团过滤
            $model = $model->where("bloc_id", \Auth::user()->bloc_id);

            if (!($model->getModel() instanceof TicketHuaShuGroup || $model->getModel() instanceof TicketHuaShu)) {
                // 按员工过滤
                $staff = Staff::query()->where("user_id", \Auth::user()->id)->first();
                if (!empty($staff) && $staff->role && !$staff->role->is_manage && !($model->getModel() instanceof Staff)) {
                    $model = $model->where("staff_id", $staff->id);
                }
            }

        }

        return $model;
    }
}

// 数据分离-获取员工ID
if (!function_exists("get_staff_id")) {
    function get_staff_id() {
        if (!Auth::check() || Auth::user()->user_type == 'admin') return 0;

        if (empty(\Auth::user()->staff_id)) {
            $staff = Staff::query()->where("user_id", \Auth::user()->id)->first();
            if (!empty($staff)) {
                return $staff->id;
            }
        }

        return (int) \Auth::user()->staff_id;
    }
}


if (!function_exists('hlen_plus')) {
    function hlen_plus($redis_key, $staff_id = 0) {
        if (!$staff_id) {
            $staff = auth()->user()->staffInfo;
            if ($staff) {
                $staff_id = $staff->id;
            }
        }
        $user = auth()->user();

        if ($user && $user->user_type != 'admin') {
            if ($user->staffInfo->role->is_manage) {
                $redis_key = $redis_key . ":bloc:" . $user->bloc_id;
                return \Illuminate\Support\Facades\Redis::hlen($redis_key);
            }
            $redis_key = $redis_key . ":" . $staff_id;
            return \Illuminate\Support\Facades\Redis::hlen($redis_key);
        }

        return \Illuminate\Support\Facades\Redis::hlen($redis_key);
    }

    function hset_plus($redis_key, $field, $val = 1, $staff_id = 0) {
        $keys = [$redis_key];

        $user = auth()->user();
        if ($user && $user->user_type != 'admin') {
            if (!$staff_id) {
                $staff = auth()->user()->staffInfo;
                if ($staff) {
                    $staff_id = $staff->id;
                }
            }

            if (!empty($staff_id)) {
                $staff = Staff::find($staff_id);
                $keys[] = $redis_key . ":bloc:" . $staff->bloc_id;
            }

            $keys[] = $redis_key . ":" . $staff_id;
        }

        foreach ($keys as $key) {
            \Illuminate\Support\Facades\Redis::hset($key, $field, $val);
        }

        // 多加一个声音的缓存 声音的播放一次，立即删除
        foreach ($keys as $key) {
            \Illuminate\Support\Facades\Redis::hset('audio:' . $key, $field, $val);
        }
    }

    function hget_plus($redis_key, $field, $staff_id = 0) {
        if (!$staff_id) {
            $staff = auth()->user()->staffInfo;
            if ($staff) {
                $staff_id = $staff->id;
            }
        }

        $user = auth()->user();
        if ($user && $user->user_type != 'admin') {
            if ($user->staffInfo->role->is_manage) {
                $redis_key = $redis_key . ":bloc:" . $user->bloc_id;
            } else {
                $redis_key = $redis_key . ":" . $staff_id;
            }
        }

        return \Illuminate\Support\Facades\Redis::hget($redis_key, $field);
    }

    function hdel_plus($redis_key, $field) {
        $keys = \Illuminate\Support\Facades\Redis::keys($redis_key . "*");
        if (!empty($keys)) {
            foreach ($keys as $key) {
                \Illuminate\Support\Facades\Redis::hdel($key, $field);
            }
        }

    }

    function set_plus($redis_key, $val = 1, $staff_id = 0) {
        $keys = [$redis_key];

        $user = auth()->user();
        if ($user && $user->user_type != 'admin') {
            if (!$staff_id) {
                $staff = auth()->user()->staffInfo;
                if ($staff) {
                    $staff_id = $staff->id;
                }
            }

            if (!empty($staff_id)) {
                $staff = Staff::find($staff_id);
                $keys[] = $redis_key . ":bloc:" . $staff->bloc_id;
            }

            $keys[] = $redis_key . ":" . $staff_id;
        }

        foreach ($keys as $key) {
            \Illuminate\Support\Facades\Redis::set($key, $val);
        }

        // 多加一个声音的缓存 声音的播放一次，立即删除
        foreach ($keys as $key) {
            \Illuminate\Support\Facades\Redis::set('audio:' . $key, $val);
        }
    }

    function get_plus($redis_key, $staff_id = 0) {
        if (!$staff_id) {
            $staff = auth()->user()->staffInfo;
            if ($staff) {
                $staff_id = $staff->id;
            }
        }

        $user = auth()->user();
        if ($user && $user->user_type != 'admin') {
            if ($user->staffInfo->role->is_manage) {
                $redis_key = $redis_key . ":bloc:" . $user->bloc_id;
            } else {
                $redis_key = $redis_key . ":" . $staff_id;
            }
        }

        return \Illuminate\Support\Facades\Redis::get($redis_key);
    }

    function del_plus($redis_key, $staff_id = 0) {
        if (!$staff_id) {
            $staff = auth()->user()->staffInfo;
            if ($staff) {
                $staff_id = $staff->id;
            }
        }
        $user = auth()->user();

        if ($user && $user->user_type != 'admin') {
            if ($user->staffInfo->role->is_manage) {
                $redis_key = $redis_key . ":bloc:" . $user->bloc_id;
                return \Illuminate\Support\Facades\Redis::del($redis_key);
            }
            $redis_key = $redis_key . ":" . $staff_id;
            return \Illuminate\Support\Facades\Redis::del($redis_key);
        }

        return \Illuminate\Support\Facades\Redis::del($redis_key);
    }
}

// 倒计时
if (!function_exists('countDown')) {
    function countDown($time){
        $timeNow = time();
        $timeOver = !is_numeric($time) ? strtotime($time) : $time;
        if ($timeOver <= $timeNow) return 0;

        $day = intval(($timeOver-$timeNow)/86400);
        $hour = intval((($timeOver-$timeNow)%86400)/3600);
        $minute = intval(((($timeOver-$timeNow)%86400)%3600)/60);
        $second = intval(((($timeOver-$timeNow)%86400)%3600)%60);

        return join(' ', [$day, translate('Day'), $hour, translate('Hours'), $minute, translate("Minutes"), $second, translate('Seconds')]);
    }
}

if (!function_exists('http_post')) {
    function http_post($url, $param = [], $json = false, $headers = [], $timeout = 0, $user = '', $pwd = '')
    {
        $curl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        if ($json && is_array($param)) {
            $param = json_encode($param);
        }

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        if(!empty($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); //设置请求头
        }else{
            curl_setopt($curl, CURLOPT_HEADER, false);
        }

        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        if ($timeout) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        }

        // Basic Auth
        if (!empty($user) && !empty($pwd)) {
            curl_setopt($curl, CURLOPT_USERPWD, $user . ':' . $pwd);
        }

        if ($json) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($param))
            );
        }

        $content = curl_exec($curl);
        $status  = curl_getinfo($curl);
        $error  = curl_error($curl);

        curl_close($curl);
        if (intval($status["http_code"]) == 200) {
            return $content;
        } else {
            return false;
        }
    }
}

if (!function_exists('curlS')) {
    function curlS($url, $return_array, $header = []){
        if(!$header){
            $header=["Content-type: application/x-www-form-urlencoded"];
        }
        if(is_array($return_array)){
            $postData = "";
            foreach ($return_array as $key => $val) {
                $postData = $postData . $key . "=" . $val . "&";
            }
            $postData = rtrim($postData, '&');
        }else{
            $postData = $return_array;
        }
        $ch        = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $contents = curl_exec($ch);
        \Illuminate\Support\Facades\Log::debug(var_export(['curlSResult' => $contents,$url, $return_array, curl_error($ch), curl_getinfo($ch)], true));

        curl_close($ch);

        return $contents;
    }
}

if (!function_exists("getExchangeRate")) {
    /**
     * 美元兑换印尼盾
     * author: Sym
     * time: 2023-05-07 17:23
     * @param string $payment_type
     * @return int|mixed
     */
    function getExchangeRate($payment_type) {
        if ($payment_type == 'htpay') {
            // 印尼 HTPAY
            return env('HTPAY_EXCHANGE_RATE', 1);
        } elseif ($payment_type == 'india_htpay') {
            //　印度HTPAY
            return env('HTPAY_EXCHANGE_RATE_IN', 1);
        }  elseif ($payment_type == 'qepay') {
            // 印尼 QEPAY
            return env('QEPAY_EXCHANGE_RATE', 1);
        }

        return 1;
    }
}


if (!function_exists('appendTicketFiles')) {
    function appendTicketFiles($list) {
        foreach ($list as $key => $value) {
            $list[$key]->created_time = date('m-d H:i', strtotime($value->created_at));

            // files
            $file_ids = $value->files ? explode(',', $value->files) : [];
            $file_list = [];
            if ($file_ids) {
                foreach (Upload::query()->whereIn('id', $file_ids)->get() as $asset) {
                    $file_list[] = $asset->external_link == null ? my_asset($asset->file_name) : $asset->external_link;
                }
            }
            $list[$key]->file_list = $file_list;
        }

        return $list;
    }

}

if (!function_exists('load_new_reply')) {
    function load_new_reply(\Illuminate\Http\Request $request) {
        $check = $request->check ?? false;
        $user_id = Auth::user()->id;

        $ticket_id = $request->ticket_id;
        if ($check) {
            $ticket = Ticket::query()->where('user_id', $user_id)->where("type", 'service')->latest('id')->first();
            $ticket_id = $ticket->id;
        }
        $list = TicketReply::query()
            ->where('user_id', '!=', $user_id);

        if ($request->last_reply_id) {
            $list = $list->where('id', '>', $request->last_reply_id);
        } else {
            $list = $list->where('read', 0);
        }

        // 只检测有多少未读
        if ($check) {
            if (Session::get('reply_notice') && $check != 2) {
                return response()->json(['success' => 1, 'count' => 0, 'session_val' => Session::get('reply_notice')]);
            }

            if (Auth::user()->user_type == 'seller' || Auth::user()->user_type == 'customer') {
                $list = $list->where('ticket_id', $ticket_id);
            }

            $tips_key = Auth::user()->user_type == 'seller' || Auth::user()->user_type == 'customer' ? 'loop_load_new_reply_audio_frontend' : 'loop_load_new_reply_audio_backend';
            if (empty(Cache::get($tips_key)) && $check == 2){
                return response()->json(['success' => 2, 'count' => 0, 'k' => $tips_key]);
            }

            $tag_names = '';
            $count = $list->count();
            if ($count > 0) {
                try {
                    // 只提示一次，Session周期内
                    Session::put('reply_notice', 1);

                    $tag_names = join(",", Ticket::query()->whereIn('id', $list->pluck('ticket_id')->toArray())->pluck('tag_name')->toArray());
                } catch (\Exception $exception) {

                }
            }

            if ($check == 2) {
                Cache::delete($tips_key);
            }

            return response()->json(['success' => 1, 'count' => $count, 'tag_names' => $tag_names]);
        }

        $list = $list->where('ticket_id', $ticket_id);
        $list = $list->orderBy('id')->get();

        if ($list->count()) {
            TicketReply::query()
                ->where('ticket_id', $ticket_id)
                ->where('user_id', '!=', $user_id)
                ->where('read', 0)
                ->update(['read' => 1]);

            $list = appendTicketFiles($list);

            // 消息已读后，立即清除缓存
            del_plus('new_ticket_tip');
            del_plus('new_work_order_ticket_tip');
        }

        return response()->json(['success' => 1, 'list' => $list]);
    }
}

/**
 * 工单打招呼
 */
if (!function_exists('ticket_say_hello')) {
    function ticket_say_hello() {
        $ticket = new Ticket;
        $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)).date('s');
        $ticket->user_id = Auth::user()->id;
        $ticket->bloc_id = Auth::user()->bloc_id;
        $ticket->staff_id = get_staff_id();
        $ticket->subject = 'Tiktok Shop Serve';
        $ticket->viewed = 0;
        $ticket->type = 'service';
        $ticket->status = 'pending';
        $ticket->details = '';
        $ticket->files = '';

        if($ticket->save()) {
            $ticket_reply = new TicketReply;
            $ticket_reply->ticket_id = $ticket->id;
            $ticket_reply->user_id = Auth::user()->id;
            $ticket_reply->reply = translate('Hello');
            $ticket_reply->files = '';
            $ticket_reply->save();

            hset_plus('new_ticket_tip', $ticket->id, 1, $ticket->staff_id);

            return $ticket->id;
        }

        return false;
    }
}

/**
 * 检测当前集团是否开始此支付
 */
if (!function_exists('is_open_this_payment')) {
    function is_open_this_payment($payment_code, $model) {
        return in_array($model->shop->bloc_id, explode(",", get_setting($payment_code. '_bloc_ids')));
    }
}
if (!function_exists("get_device_type")) {
    function get_device_type() {

        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        $type = 'other';
        //分别进行判断
        if(strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            $type = 'ios';
        }

        if(strpos($agent, 'android')) {
            $type = 'android';
        }

        return $type;
    }

    function is_android() {
        return 'android' === get_device_type();
    }

    function is_ios() {
        return 'ios' === get_device_type();
    }

    function is_pc() {
        return 'other' === get_device_type();
    }

    function is_mobile(){
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';
        if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
            $mobile_browser++;
        if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
            $mobile_browser++;
        if(isset($_SERVER['HTTP_X_WAP_PROFILE']))
            $mobile_browser++;
        if(isset($_SERVER['HTTP_PROFILE']))
            $mobile_browser++;
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda','xda-'
        );
        if(in_array($mobile_ua, $mobile_agents))
            $mobile_browser++;
        if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
            $mobile_browser++;
        // Pre-final check to reset everything if the user is on Windows
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
            $mobile_browser=0;
        // But WP7 is also Windows, with a slightly different characteristic
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
            $mobile_browser++;
        if($mobile_browser>0)
            return true;
        else
            return false;
    }
}

if (!function_exists('getPaymentCountries')) {
    function getPaymentCountries() {
        $payment_countries = \App\Models\Country::query()
            ->where('status', 1)
            ->whereIn('code', ['cn', 'ID', 'IN', 'tr'])
            ->orderBy('name')
            ->get();

        return $payment_countries;
    }
}
