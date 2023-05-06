<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Language;
use App\Models\Product;
use App\Models\Review;
use App\Models\SellerPackage;
use App\Models\SellerPackagePayment;
use App\Models\Shop;
use App\Models\Translation;
use App\Models\User;
use App\Services\ProductStockService;
use App\Services\ProductTaxService;
use Illuminate\Console\Command;

class ShopReviewInit extends Command
{
    protected $productTaxService;
    protected $productStockService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'review:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init Shop and products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->productTaxService = new ProductTaxService();
        $this->productStockService = new ProductStockService();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('reviews init......');

        try {
            $shops = Shop::all();
            $pb = $this->output->createProgressBar(Product::query()->where(['added_by' => 'seller'])->count() + $shops->count());
            Product::query()->where(['added_by' => 'seller'])->chunk(100, function ($products) use ($pb) {
//                $this->info(count($products));
                foreach ($products as $product) {
                    if(Review::where('product_id', $product->id)->where('status', 1)->count() > 0){
                        $product->rating = Review::where('product_id', $product->id)->where('status', 1)->sum('rating')/Review::where('product_id', $product->id)->where('status', 1)->count();
                    }
                    else {
                        $product->rating = 0;
                    }
                    $product->save();

                    $pb->advance(1);
                }
            });

            foreach ($shops as $shop) {
                $shopProductIds = Product::query()->where('user_id', $shop->user_id)->pluck('id');
                if (empty($shopProductIds)) continue;

                $num_of_reviews = Review::whereIn('product_id', $shopProductIds)->where('status', 1)->count();
                if (empty($num_of_reviews)) continue;

                $rating = Review::whereIn('product_id', $shopProductIds)->where('status', 1)->sum('rating')/$num_of_reviews;
                $shop->rating = $rating;
                $shop->num_of_reviews = $num_of_reviews;
                $shop->save();

                $pb->advance(1);
            }

            $pb->finish();
        }catch (\Exception $exception) {
            $this->info($exception->getMessage());
        }
    }
}
