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
    protected $signature = 'shop:init {num?}';

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
     * @throws \Throwable
     */
    public function handle()
    {
        $this->info('start init shop......');

        $num = 3;
        if (!empty($this->argument('num')) and is_numeric($this->argument('num'))) {
            $num = $this->argument('num');
        }

        echo "num=" . $num . PHP_EOL;

        $package = SellerPackage::where(['is_default' => 1 ])->first();
        $package_id = $package['id'];

        $bloc_id = 0;
        $staff_id = 0;

        $pb = $this->output->createProgressBar($num);
        for ($i = 0; $i < $num; $i++) {
            // 生成店铺
            $faker = \Faker\Factory::create();
            $user = new User();
            $user->name = $faker->name;
            $user->is_virtual_user = 1;
            $user->bloc_id = $bloc_id;
            $user->staff_id = $staff_id;
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
                $shop->name = $user->name . " Shop";
                $shop->address = '';
                $shop->slug = preg_replace('/\s+/', '-', $user->name) . '-' . $user->id;
                $shop->seller_package_id = $package_id;
                $shop->verification_status = 1;
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

            $pb->advance(1);
        }

        $pb->finish();
    }
}
