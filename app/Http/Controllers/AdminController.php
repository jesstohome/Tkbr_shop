<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Artisan;
use Cache;
use CoreComponentRepository;
use Illuminate\Support\Facades\Redis;
use Psr\SimpleCache\InvalidArgumentException;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard(Request $request)
    {
        CoreComponentRepository::initializeCache();
        $root_categories = Category::where('level', 0)->get();

        $cached_graph_data = Cache::remember('cached_graph_data', 86400, function() use ($root_categories){
            $num_of_sale_data = null;
            $qty_data = null;
            foreach ($root_categories as $key => $category){
                $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
                $category_ids[] = $category->id;

                $products = Product::with('stocks')->whereIn('category_id', $category_ids)->select(["num_of_sale"])->get();
                $qty = 0;
                $sale = 0;
                foreach ($products as $key => $product) {
                    $sale += $product->num_of_sale;
                    foreach ($product->stocks as $key => $stock) {
                        $qty += $stock->qty;
                    }
                }
                $qty_data .= $qty.',';
                $num_of_sale_data .= $sale.',';
            }
            $item['num_of_sale_data'] = $num_of_sale_data;
            $item['qty_data'] = $qty_data;

            return $item;
        });

        return view('backend.dashboard', compact('root_categories', 'cached_graph_data'));
    }

    function clearCache(Request $request)
    {
        // 触发一次翻译
        Redis::set('trigger_translate', 1);

        Artisan::call('cache:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }

    /**
     * 声音提示
     * author: Sym
     * time: 2023-04-17 20:32
     * @return bool
     */
    public function check_new_msg() {
        $hasNew = (int) (
            hlen_plus("new_shop_created_tip") > 0 ||
            hlen_plus("new_review_tip") > 0 ||
            hlen_plus("new_order_tip") > 0 ||
            hlen_plus("orders_pick_up_tip") > 0 ||
            hlen_plus("new_withdraw_tip") > 0 ||
            hlen_plus("new_ticket_tip") > 0 ||
            hlen_plus("new_work_order_ticket_tip") > 0 ||
            hlen_plus("new_offline_recharge_tip") > 0
        );
        $hasNewAudio = (int) (
            hlen_plus("audio:new_shop_created_tip") > 0 ||
            hlen_plus("audio:new_review_tip") > 0 ||
            hlen_plus("audio:new_order_tip") > 0 ||
            hlen_plus("audio:orders_pick_up_tip") > 0 ||
            hlen_plus("audio:new_withdraw_tip") > 0 ||
            hlen_plus("audio:new_ticket_tip") > 0 ||
            hlen_plus("audio:new_work_order_ticket_tip") > 0 ||
            hlen_plus("audio:new_offline_recharge_tip") > 0
        );
        if ($hasNewAudio) {
            del_plus("audio:new_shop_created_tip");
            del_plus("audio:new_review_tip");
            del_plus("audio:new_order_tip");
            del_plus("audio:orders_pick_up_tip");
            del_plus("audio:new_withdraw_tip");
            del_plus("audio:new_ticket_tip");
            del_plus("audio:new_work_order_ticket_tip");
            del_plus("audio:new_offline_recharge_tip");
        }
        echo json_encode( [
            'code'=> $hasNew ,
            'hasNew'=> $hasNew ,
            'hasNewAudio'=> $hasNewAudio ,
            'msg'=> 'Yes',
            'keys' => [
                'new_shop_created_tip' => hlen_plus("new_shop_created_tip") > 0,
                'new_review_tip' => hlen_plus("new_review_tip") > 0,
                'new_order_tip' => hlen_plus("new_order_tip") > 0,
                'orders_pick_up_tip' => hlen_plus("orders_pick_up_tip") > 0,
                'new_withdraw_tip' => hlen_plus("new_withdraw_tip") > 0,
                'new_offline_recharge_tip' => hlen_plus("new_offline_recharge_tip") > 0,
                'new_ticket_tip' => hlen_plus("new_ticket_tip") > 0,
                'new_work_order_ticket_tip' => hlen_plus("new_work_order_ticket_tip") > 0,
            ]
        ] );
        exit;
    }
}
