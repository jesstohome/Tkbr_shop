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

class ShopInit extends Command
{
    protected $productTaxService;
    protected $productStockService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:init';

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
        $this->info('start init shop......');

        $num = 3;
        if (!empty($this->argument('num')) and is_numeric($this->argument('num'))) {
            $num = $this->argument('num');
        }

        $package = SellerPackage::where(['is_default' => 1 ])->first();
        $package_id = $package['id'];

        $bloc_id = 0;
        $staff_id = 0;

        $customerIds = User::query()->where('user_type', 'customer')->pluck('id')->toArray();
        $productIds = Product::query()->where('in_storehouse', 1)->pluck('id')->toArray();
        $pb = $this->output->createProgressBar($num);
        for ($i = 0; $i < $num; $i++) {
            // 生成店铺
            $faker = \Faker\Factory::create();
            $user = new User();
            $user->name = $faker->name;
            $user->is_virtual_user = 1;
            $user->bloc_id = $bloc_id;
            $user->staff_id = $staff_id;
            //$user->password = bcrypt('test');
            $user->email = $faker->email;
            $user->email_verified_at = \date('Y-m-d H:i:s');
            $user->balance = 0;
            $user->user_type = "seller";
            $user->creditscore = "60";
            $user->saveOrFail();
            if ( Shop::where('user_id', $user->id)->first() == NULL ) {
                $shop = new Shop;
                $shop->bloc_id = $bloc_id;
                $shop->staff_id = $staff_id;
                $shop->user_id = $user->id;
                $shop->name = $user->name;
                $shop->address = '';
                $shop->slug = preg_replace('/\s+/', '-', $user->name);
                $shop->seller_package_id = $package_id;
                $shop->save();

                $seller_package = new SellerPackagePayment;
                $seller_package->user_id = $user->id;
                $seller_package->seller_package_id =  $package_id;
                $seller_package->payment_method = 'free';
                $seller_package->payment_details = '';
                $seller_package->approval = 1;
                $seller_package->offline_payment = 0;
                $seller_package->save();
            }

            $offset = empty($limit) ? 0 : $limit;
            $limit = mt_rand(20, 40);
            $partProductIds = array_slice($productIds, $offset, $limit);

            // 上传产品
            $maxProfit = $package->max_profit / 100;
            foreach ($partProductIds as $productId) {
                $product = Product::find($productId);
                $profitPrice = $product->unit_price * $maxProfit;

                $product_new = $product->replicate();
                $product_new->slug = $product_new->slug . '-' . \Str::random(5);
                $product_new->added_by = 'seller';
                $product_new->user_id = $user->id;
                $product_new->bloc_id = $bloc_id;
                $product_new->staff_id = $staff_id;
                $product_new->unit_price = $product->unit_price + $profitPrice;
                $product_new->original_id = $productId;
                $product_new->published = 1;
                $product_new->in_storehouse = 0;
                $product_new->save();

                // 循环复制产品翻译
                foreach ($product->product_translations as $productTranslation) {
                    $productTranslationNew = $productTranslation->replicate();
                    $productTranslationNew->product_id = $product_new->id;
                    $productTranslationNew->save();
                }

                //Product Stock
                $this->productStockService->product_duplicate_store($product->stocks, $product_new, $profitPrice);

                //VAT & Tax
                $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

                // 生成评论
                foreach ( $comments as $comment) {
                    $reviewUserIdIndex = array_rand($customerIds, 1);
                    $reviewModel = new Review;
                    $reviewModel->bloc_id = $bloc_id;
                    $reviewModel->staff_id = $staff_id;
                    $reviewModel->product_id = $product_new->id;
                    $reviewModel->user_id = $customerIds[$reviewUserIdIndex];
                    $reviewModel->rating = mt_rand(4, 5);
                    $reviewModel->comment = $comment;
                    $reviewModel->viewed = '0';
                    $reviewModel->save();
                }
            }
            
            $pb->advance(1);
        }

        $pb->finish();
    }
}
