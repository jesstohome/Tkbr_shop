<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Conversation;
use App\Models\EmailTask;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\ProductStock;
use App\Models\Product;
use App\Models\Order;
use App\Models\City;
use App\Models\User;
use App\Models\Address;
use App\Models\Addon;
use Illuminate\Support\Carbon;
use Session;
use Auth;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Http\Resources\PosProductCollection;
use App\Models\Country;
use App\Models\State;
use App\Utility\CategoryUtility;
use function get_setting;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $product_id = $request->product_id;
        $customer_id = $request->customer_id;
        $seller_id = $request->get('seller_id', Session::get('seller_id', 0));
        $customers = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->where('bloc_id', '>', 0)->orderBy('created_at', 'desc');
        $customers = filter_by_bloc($customers);
        $customers = $customers->get();
        $product = Product::find($product_id);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('pos.index', compact('customers', 'seller_id', 'product', 'customer_id'));
        }elseif (Auth::user()->user_type == 'salesman'){

            $customers = User::where('user_type', 'customer')->where('referred_by', '=', Auth::user()->id )->orderBy('created_at', 'desc');
            $customers = filter_by_bloc($customers);
            $customers = $customers->get();

            return view('pos.salesman.index', compact('customers'));
        }
        else {
            if (get_setting('pos_activation_for_seller') == 1) {
                flash(translate('POS is disable for Sellers!!!'))->error();
                return back();
//                return view('pos.frontend.seller.pos.index', compact('customers'));
            }
            else {
                flash(translate('POS is disable for Sellers!!!'))->error();
                return back();
            }
        }
    }

    public function search(Request $request)
    {
        if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
            $products = ProductStock::join('products','product_stocks.product_id', '=', 'products.id')->select('products.*','product_stocks.id as stock_id','product_stocks.variant','product_stocks.price as stock_price', 'product_stocks.qty as stock_qty', 'product_stocks.image as stock_image')
                ->whereNotNull('products.original_id');
        }elseif (Auth::user()->user_type == 'salesman'){
            $products = ProductStock::join('products','product_stocks.product_id', '=', 'products.id')->select('products.*','product_stocks.id as stock_id','product_stocks.variant','product_stocks.price as stock_price', 'product_stocks.qty as stock_qty', 'product_stocks.image as stock_image');
            $users = User::where('pid', Auth::user()->id)->get()->toArray();
            $products = $products->whereIn('products.user_id', array_column($users, 'id'));
        }
        else {
            $products = ProductStock::join('products','product_stocks.product_id', '=', 'products.id')->where('user_id', Auth::user()->id)->where('published', '1')->select('products.*','product_stocks.id as stock_id','product_stocks.variant','product_stocks.price as stock_price', 'product_stocks.qty as stock_qty', 'product_stocks.image as stock_image');
        }

         $products = $products->where('published', '1')->where("approved", 1);

        if($request->category != null){
            $arr = explode('-', $request->category);
            if($arr[0] == 'category'){
                $category_ids = CategoryUtility::children_ids($arr[1]);
                $category_ids[] = $arr[1];
                $products = $products->whereIn('products.category_id', $category_ids);
            }
        }
        if($request->user_id != null){
            $products = $products->where('products.user_id', $request->user_id);
        }

        if($request->brand != null){
            $products = $products->where('products.brand_id', $request->brand);
        }

        if ($request->keyword != null) {
            $products = $products->where('products.name', 'like', '%'.$request->keyword.'%')->orWhere('products.barcode', $request->keyword);
        }

        $products = filter_by_bloc($products);

        if (!empty($request->order_by_price)) {
            $products = $products->orderBy('product_stocks.price', $request->order_by_price);
        } else {
            $products = $products->orderBy('products.created_at', 'desc');
        }

        $products = $products->paginate(16);
        $page_links = $products->appends(request()->query())->links('partials.paginate')->render();

        $stocks = new PosProductCollection($products);
        $stocks->appends(['keyword' =>  $request->keyword,'category' => $request->category, 'brand' => $request->brand, 'user_id' => $request->user_id, 'order_by_price' => $request->order_by_price]);
        return ['page_links' => $page_links, 'products' => $stocks];
    }

    public function addToCart(Request $request)
    {
        $stock = ProductStock::find($request->stock_id);
        $product = $stock->product;

        $data = array();
        $data['stock_id'] = $request->stock_id;
        $data['id'] = $product->id;
        $data['variant'] = $stock->variant;
        $data['quantity'] = $product->min_qty;

        if($stock->qty < $product->min_qty){
            return array('success' => 0, 'message' => translate("This product doesn't have enough stock for minimum purchase quantity ").$product->min_qty, 'view' => view('pos.cart')->render());
        }

        $tax = 0;
        $price = $stock->price;

        // discount calculation
        $discount_applicable = false;
        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        }
        elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
            $discount_applicable = true;
        }
        if ($discount_applicable) {
            if($product->discount_type == 'percent'){
                $price -= ($price*$product->discount)/100;
            }
            elseif($product->discount_type == 'amount'){
                $price -= $product->discount;
            }
        }

        //tax calculation
        foreach ($product->taxes as $product_tax) {
            if($product_tax->tax_type == 'percent'){
                $tax += ($price * $product_tax->tax) / 100;
            }
            elseif($product_tax->tax_type == 'amount'){
                $tax += $product_tax->tax;
            }
        }

        $data['price'] = $price;
        $data['tax'] = $tax;

        if($request->session()->has('pos.cart')){
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('pos.cart') as $key => $cartItem){
                if($cartItem['id'] == $product->id && $cartItem['stock_id'] == $stock->id){
                    $foundInCart = true;
                    $loop_product = Product::find($cartItem['id']);
                    $product_stock = $loop_product->stocks->where('variant', $cartItem['variant'])->first();

                    if($product_stock->qty >= ($cartItem['quantity'] + 1)){
                        $cartItem['quantity'] += 1;
                    }else{
                        return array('success' => 0, 'message' => translate("This product doesn't have more stock."), 'view' => view('pos.cart')->render());
                    }
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('pos.cart', $cart);
        }
        else{
            $cart = collect([$data]);
            $request->session()->put('pos.cart', $cart);
        }

        $request->session()->put('pos.cart', $cart);

        return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('pos.cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key){
                $product = Product::find($object['id']);
                $product_stock = $product->stocks->where('id', $object['stock_id'])->first();

                if($product_stock->qty >= $request->quantity){
                    $object['quantity'] = $request->quantity;
                }else{
                    return array('success' => 0, 'message' => translate("This product doesn't have more stock."), 'view' => view('pos.cart')->render());
                }
            }
            return $object;
        });
        $request->session()->put('pos.cart', $cart);

        return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if(Session::has('pos.cart')){
            $cart = Session::get('pos.cart', collect([]));
            $cart->forget($request->key);
            Session::put('pos.cart', $cart);

            $request->session()->put('pos.cart', $cart);
        }

        return view('pos.cart');
    }

    //Shipping Address for admin
    public function getShippingAddress(Request $request){
        $user_id = $request->id;
        if($user_id == ''){
            return view('pos.guest_shipping_address');
        }
        else{
            $address = Address::where('user_id',$user_id)->get();
            if ($address->count() == 1) {
                $address = $address[0];
                $data['name'] = $address->user->name;
                $data['email'] = $address->user->email;
                $data['address'] = $address->address;
                $data['country'] = $address->country->name;
                $data['state'] = $address->state->name;
                $data['city'] = $address->city->name;
                $data['postal_code'] = $address->postal_code;
                $data['phone'] = $address->phone;
                $shipping_info = $data;
                $request->session()->put('pos.shipping_info', $shipping_info);
                return '';
            }
            return view('pos.shipping_address', compact('user_id'));
        }
    }

    //Shipping Address for seller
    public function getShippingAddressForSeller(Request $request){
        $user_id = $request->id;
        if($user_id == ''){
            return view('pos.frontend.seller.pos.guest_shipping_address');
        }
        else{
            return view('pos.frontend.seller.pos.shipping_address', compact('user_id'));
        }
    }

    public function set_shipping_address(Request $request) {
        if ($request->address_id != null) {
            $address = Address::findOrFail($request->address_id);
            $data['name'] = $address->user->name;
            $data['email'] = $address->user->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country->name;
            $data['state'] = $address->state->name;
            $data['city'] = $address->city->name;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = Country::find($request->country_id)->name;
            $data['state'] = State::find($request->state_id)->name;
            $data['city'] = City::find($request->city_id)->name;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
        }

        $shipping_info = $data;
        $request->session()->put('pos.shipping_info', $shipping_info);
    }

    //set Discount
    public function setDiscount(Request $request){
        if($request->discount >= 0){
            Session::put('pos.discount', $request->discount);
        }
        return view('pos.cart');
    }

    //set Shipping Cost
    public function setShipping(Request $request){
        if($request->shipping != null){
            Session::put('pos.shipping', $request->shipping);
        }
        return view('pos.cart');
    }

    //order summary
    public function get_order_summary(Request $request){
        return view('pos.order_summary');
    }

    //order place
    public function order_store(Request $request){
        try {
        if(Session::get('pos.shipping_info') == null || Session::get('pos.shipping_info')['name'] == null || Session::get('pos.shipping_info')['phone'] == null || Session::get('pos.shipping_info')['address'] == null){
            return array('success' => 0, 'message' => translate("Please Add Shipping Information."));
        }



        if(Session::has('pos.cart') && count(Session::get('pos.cart')) > 0){
            $order = new Order;
            $order->bloc_id = Auth::user()->bloc_id;
            $order->staff_id = get_staff_id();
            $shipping_info = Session::get('pos.shipping_info');
            if ($request->user_id == null) {
                $order->guest_id    = mt_rand(100000, 999999);
            }
            else {
                $order->user_id = $request->user_id;
            }

            $data['name']           = $shipping_info['name'];
            $data['email']          = $shipping_info['email'];
            $data['address']        = $shipping_info['address'];
            $data['country']        = $shipping_info['country'];
            $data['city']           = $shipping_info['city'];
            $data['postal_code']    = $shipping_info['postal_code'];
            $data['phone']          = $shipping_info['phone'];
            $order->shipping_address = json_encode($data);

            $order->payment_type = $request->payment_type;
            $order->add_by_admin = 1;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His').rand(10,99);
            $order->date = strtotime('now');
            $order->payment_status = $request->payment_type != 'cash_on_delivery' ? 'paid' : 'unpaid';
            $order->payment_details = $request->payment_type;
            $effectivetime = $request->effectivetime;
            $order_type = $request->order_type;
            if ($effectivetime != null) {
                $order->updated_at = $effectivetime;
                $order->created_at = $effectivetime;
            }

            //return array('success' => 0, 'message' => $effectivetime);
            $order->order_type = $order_type;
            //return array('success' => 0, 'message' => translate($today. ' '.$order_type));
            if($request->payment_type == 'offline_payment'){
                if($request->offline_trx_id == null){
                    return array('success' => 0, 'message' => translate("Transaction ID can not be null."));
                }
                $data['name']   = $request->offline_payment_method;
                $data['amount'] = $request->offline_payment_amount;
                $data['trx_id'] = $request->offline_trx_id;
                $data['photo']  = $request->offline_payment_proof;
                $order->manual_payment_data = json_encode($data);
                $order->manual_payment = 1;
            }


            $order->picking_switch = get_setting('picking_switch');
            if($order->save()){
                $subtotal = 0;
                $tax = 0;
                $shipping_cost = 0;
                $productStorehouseTotal = 0;
                foreach (Session::get('pos.cart') as $key => $cartItem){
                    $product_stock = ProductStock::find($cartItem['stock_id']);
                    if ($product_stock){
                        $product = $product_stock->product;
                        $product_variation = $product_stock->variant;

                        $subtotal += $cartItem['price']*$cartItem['quantity'];
                        $tax += $cartItem['tax']*$cartItem['quantity'];

                        // 计算产品仓库的产品货款
                        $originalProduct = null;
                        if ($product->original_id) {
                            $originalProduct = Product::query()->find($product->original_id);
                            if ($originalProduct) {
                                $productStorehouseTotal += cart_product_price($cartItem, $originalProduct, false, false) * $cartItem['quantity'];
                            }
                        }

                        if($cartItem['quantity'] > $product_stock->qty){
                            $order->delete();
                            return array('success' => 0, 'message' => $product->name.' ('.$product_variation.') '.translate(" just stock outs."));
                        }
                        else {
                            $product_stock->qty -= $cartItem['quantity'];
                            $product_stock->save();
                        }

                        $order_detail = new OrderDetail;
                        $order_detail->order_id = $order->id;
                        $order_detail->seller_id = $product->user_id;
                        $order_detail->product_id = $product->id;
                        $order_detail->payment_status = $request->payment_type != 'cash_on_delivery' ? 'paid' : 'unpaid';
                        $order_detail->variation = $product_variation;
                        $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                        $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                        $order_detail->quantity = $cartItem['quantity'];
                        $order_detail->shipping_type = null;

                        if ($product->shipping_cost > 0) {
                            $order_detail->shipping_cost = $product->shipping_cost;
                            $shipping_cost += $product->shipping_cost;
                        }
                        else {
                            $order_detail->shipping_cost = 0;
                        }

                        $order_detail->save();

                        $product->num_of_sale++;
                        $product->save();
                    }
                }

                $order->grand_total = $subtotal + $tax + $shipping_cost;

                if(Session::has('pos.discount')){
                    $order->grand_total -= Session::get('pos.discount');
                    $order->coupon_discount = Session::get('pos.discount');
                }

                // 查看余额是否足够
                if ($request->payment_type == 'wallet') {
                    $user = User::findOrFail($order->user_id);
                    if ($user->balance < $order->grand_total) {
                        $order->delete();
                        return array('success' => 0, 'message' => translate("Insufficient balance"));
                    }
                    $user->balance -= $order->grand_total;
                    $user->save();
                }

                $order->seller_id = $product->user_id;
                $order->product_storehouse_total = $productStorehouseTotal;
                $order->save();

                $shop = $order->shop;
                if ( get_setting('picking_switch') != 1 )
                {
                    //如果不需要提货，直接修改订单为已提货状态
                    $shop->admin_to_pay += ( $order->grand_total - $order->product_storehouse_total );
                    $shop->save();
                    // 保存订单冻结资金过期时间
                    $freezeDays = get_setting('frozen_funds_unfrozen_days', 15);
                    $order->freeze_expired_at = Carbon::now()->addDays($freezeDays)->timestamp;
                    $order->product_storehouse_status = 1;
                    $order->save();
                }

                $array['view'] = 'emails.invoice';
                $array['subject'] = 'Your order has been placed - '.$order->code;
                $array['from'] = env('MAIL_USERNAME');
                $array['order'] = $order;

                $admin_products = array();
                $seller_products = array();

                foreach ($order->orderDetails as $key => $orderDetail){
                    if($orderDetail->product->added_by == 'admin'){
                        array_push($admin_products, $orderDetail->product->id);
                    }
                    else{
                        $product_ids = array();
                        if(array_key_exists($orderDetail->product->user_id, $seller_products)){
                            $product_ids = $seller_products[$orderDetail->product->user_id];
                        }
                        array_push($product_ids, $orderDetail->product->id);
                        $seller_products[$orderDetail->product->user_id] = $product_ids;
                    }
                }

                // 邮件通知
                foreach($seller_products as $key => $seller_product){
                    try {
                        $seller = User::find($key);
                        $array['view'] = 'emails.new_order';
                        $array['subject'] = 'New Order Notice';
                        $array['from'] = env('MAIL_FROM_ADDRESS');
                        $array['content'] = '';
                        $array['seller_name'] = $seller->name;

                        $task = new EmailTask();
                        $task->email = $seller->email;
                        $task->array = json_encode($array, JSON_UNESCAPED_UNICODE);
                        $task->save();
                    } catch (\Exception $e) {

                    }
                }

                if($request->user_id != NULL){
                    if (Addon::where('unique_identifier', 'club_point')->first() != null && Addon::where('unique_identifier', 'club_point')->first()->activated) {
                        $clubpointController = new ClubPointController;
                        $clubpointController->processClubPoints($order);
                    }
                }

                calculateCommissionAffilationClubPoint($order);

                hset_plus("new_order_tip", $order->id, 1, $shop->staff_id, $shop->user_id);

                Session::put('seller_id', $shop->user_id);

                Session::forget('pos.shipping_info');
                Session::forget('pos.shipping');
                Session::forget('pos.discount');
                Session::forget('pos.cart');
               return array('success' => 1, 'message' => translate('Order Completed Successfully.'));
            }
            else {
                return array('success' => 0, 'message' => translate('Please input customer information.'));
            }

        }
        return array('success' => 0, 'message' => translate("Please select a product."));
         } catch (Exception $e) {

            return array('success' => 0, 'message' => translate($e));


            }
    }

    public function pos_activation()
    {
        return view('pos.pos_activation');
    }

    /**
     * 对话
     * author: Sym
     * time: 2023-04-08 12:31
     * @return mixed
     */
    public function pos_conversation(Request $request) {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $conversations = Conversation::where('add_by_admin', 1)->orderBy('updated_at', 'desc');
            $conversations = filter_by_bloc($conversations);

            $seller_id = $request->seller_id;
            if ($seller_id) {
                $conversations = $conversations->where("receiver_id", $seller_id);
            }
            $bloc_id = $request->bloc_id;
            if ($bloc_id) {
                $conversations = $conversations->where("bloc_id", $bloc_id);
            }
            $staff_id = $request->staff_id;
            if ($staff_id) {
                $conversations = $conversations->where("staff_id", $staff_id);
            }
            if ($request->date_range) {
                $date_range = $request->date_range;
                $date_range1 = explode("/", $request->date_range);
                $conversations = $conversations->where('updated_at', '>=', trim($date_range1[0]));
                $conversations = $conversations->where('updated_at', '<=', trim($date_range1[1]) . " 23:59:59");
            }

            $conversations = $conversations->paginate(5)->appends(request()->query());

            del_plus('new_pos_conversation_tip');

            return view('pos.conversations.index', compact('conversations', 'seller_id', 'bloc_id', 'staff_id', 'date_range'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    /**
     * 对话详情
     * author: Sym
     * time: 2023-04-08 12:31
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|mixed
     */
    public function pos_conversation_show($id) {
        $conversation = Conversation::findOrFail(decrypt($id));
        $conversation->sender_viewed = 1;
        $conversation->admin_viewed = 1;
        $conversation->save();

        $seller_id = 0;
        if (!empty($conversation->product_id)) {
            $product = Product::find($conversation->product_id);
            if (!empty($product->slug)) {
                $product_url = route('product', $product->slug);
            }
            $seller_id = $product->user_id;
        }

        $customer_id = $conversation->sender_id;
        $product_id = $conversation->product_id;
        return view('pos.conversations.show', compact('conversation', 'product_url', 'seller_id', 'product_id', 'customer_id'));
    }

    /**
     * 回复对话
     * author: Sym
     * time: 2023-04-08 12:54
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pos_conversation_message_store(Request $request) {
        $conversation = Conversation::findOrFail( $request->conversation_id );
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = $conversation->sender_id;
        $message->message = $request->message;
        $message->save();

        $conversation->sender_viewed = "1";
        $conversation->save();

        $cache_key = $conversation->add_by_admin ? 'new_pos_conversation_tip' : 'new_conversation_tip';
        if (isAdmin()) {
            $to_user = User::find($conversation->receiver_id);
            hset_plus($cache_key, $conversation->id, 1, $conversation->staff_id, $to_user->id, $to_user, true);
        } else {
            hset_plus($cache_key, $conversation->id, 1, $conversation->staff_id, $conversation->receiver_id, '', true);
        }

        return back();
    }

    /**
     * 删除对话消息
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function message_destroy(Request $request) {
        $message_id = $request->id;
        if ($message_id) {
            $message = Message::find($message_id);
            Message::destroy($message_id);

            // 消息全部删除完了，则删除对话
            if (Message::query()->where('conversation_id', $message->conversation_id)->count() == 0) {
                Conversation::destroy($message->conversation_id);
                flash(translate('Message has been deleted successfully'))->success();
                return redirect()->route('poin-of-sales.conversation');
            }

            flash(translate('Message has been deleted successfully'))->success();
            return redirect()->route('poin-of-sales.conversation-show', encrypt($message->conversation_id));
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
