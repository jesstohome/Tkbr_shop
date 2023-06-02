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
use Illuminate\Support\Facades\Log;

class ProductInit extends Command
{
    protected $productTaxService;
    protected $productStockService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:init {category_ids?} {shop_num?}';

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
        $this->info('start init products......');

        $comments = [
            "I accidentally came to your shop and found the baby I wanted, I am in a good mood!	",
            "Baby received it, I like it very much, the buyer is very trustworthy!	",
            "If there is a problem, it can be dealt with very well, and you can trust a better store. We will cooperate again in the future.	",
            " Like a fast delivery, the baby is similar to the picture.	",
            "Not bad, hope to cooperate next time, thank you shopkeeper~~	",
            "The price is popular, the quality is very good, I am very satisfied with the styles and fabrics, if necessary, I will continue to patronize your store!	",
            "Sorry for the late payment!	",
            "The goods are good, the people are better! Simple is good!	",
            "I got it yesterday, thank you!	",
            "Happy cooperation, payment has been confirmed, please check!	",
            "Very good! very good!	",
            "The product is very good, and the service attitude is also good!	",
            "Very good, very satisfied!	",
            "Thank you, boss, for sending a sincere and positive review!	",
            "I finally got it~~ I'm so happy!	",
            "The shop owner is really nice, thank you so much!	",
            "Just received the goods, very good, value for money!! Will come again in the future!	",
            "The price is very favorable, the service is also very good, and the things are good, haha...	",
            "The quality is very good, and I hope more friends will trust it.	",
            "The owner's attitude is very good, I will patronize again.	",
            "Can it be cheaper? I'll bring my friends to your house to buy.	",
            "Sorry for the late review, very good store, I like the items very much!	",
            "I bought this as a gift. She likes it very much. The owner's thoughtfulness makes me feel very warm. I will choose it again in the future.	",
            "  strongly support!	",
            "I like it very much! I also like the store, very enthusiastic~!	",
            "One word is good, two words are very good, three words are great! Haha...	",
            "Okay, support, if necessary, I will call you again next time	",
            "The reputation is very good, and the delivery is very timely	",
            "Support, strongly support!	",
            "The host is nice and very enthusiastic. I hope your business will be better and better!! Hehe	",
            "By the way!~ I can only use fast, okay, to describe it!~ Hee hee! `I will look for you next time!~	",
            "Nice shop owner, so I will keep patronizing again!	",
            "Not bad, very nice shop owner. very good, very high	",
            "Convenient and quick, thank you, happy cooperation!	",
            "I am very satisfied with the item received today, thank you boss! Remember the discount when you come next time!	",
            "Not bad, I will come again!	",
            "I am very satisfied with the item received!! I am really a super good shopkeeper, answering questions tirelessly, meticulous and conscientious, the key is that the item is good, and the goods are delivered super fast, carefully packaged, trustworthy!	",
            "Very good, very fast delivery! Attentive service!	",
            "The store's service is very thoughtful. I am very satisfied with the store's baby!	",
            "The delivery speed is very fast, I like it	",
            "It's very special, come visit again...	",
            "Very enthusiastic store owner, I will come again next time	",
            "Hope to have the opportunity to cooperate next time	",
            "  Wish you a prosperous business	",
            "The shopkeeper's service is pretty good, and I can still do business in the future#	",
            "Not bad, not bad, I like such a boss~~~~	",
            "Every time I trade with you, I am so successful and happy. I hope we will have more transactions in the future! Ha ha!	",
            "It's pretty good, hehe, I will continue to patronize in the future.	",
            "It's okay, you get what you pay for	",
            "Baby is good, the delivery is very fast!	",
            "The delivery was super fast, but I wasn't there and couldn't sign for it in time. Like Like	",
            "Very good shopkeeper, absolutely support	",
            "Received this morning, I like it very much, I will definitely come back next time	",
            "A very patient shopkeeper! The delivery is also very timely	",
            "Very good, this stuff is worth it. thank you boss	",
            "Well, this shop owner is very satisfactory. Support it.	",
            "Very good, sincere attitude, timely reply	",
            "Ha, that's really good. This boss is very authentic	",
            "It is very comfortable to do business with you. Very responsible, thank you, we will cooperate in the future.	",
            "Hehe, it's better than expected. The price is also fair, thank you~~~	",
            "I just received it this morning, and I like it very much. I will come again next time, can I get a discount for introducing friends?	",
            "The quality is very good, beyond my expectation, the packaging is very careful, thank you very much, I wish you a prosperous business!	",
            "Thank you very much, I like it so much, come again next time~	",
            "The items are good, and the owner is also very funny. Come again next time~	",
            "Received this morning, I like it very much, I will definitely come back next time	",
            "Very good seller, absolutely support	",
            "Not bad, not bad, I like such a boss~~~~	",
            "I was a little worried at first, but I went to verify it. It is a licensed product and a good seller.	",
            "The quality of the skirt is very good, it is very slim to wear, and it is very comfortable to wear to work!	",
            "Things are still ok, but a little dirty, I hope to pay more attention to the next shipment	",
            "The quality is very good and does not fade. The size recommended by the customer service is very suitable. I am very happy	",
            "I am a regular customer, I hope there will be more good things in the future	",
            "The seller's service is pretty good, and we can still do business in the future	",
            "Every time I trade with you, I am so successful and happy. I hope we will have more transactions in the future! Ha ha!	",
            "The delivery was super fast, but I wasn't there and couldn't sign for it in time. Like Like	",
            "  Great seller. Thank you. My colleagues all like it. Come again next time	",
            "The quality is very good, very good!	",
            "It's pretty good, hehe, I will continue to patronize in the future.	",
            "It's a rare authentic product, the most satisfying since online shopping.	",
            "It's really worth it. Everything is fine.	",
            "The store's goods are exquisite in workmanship, and the service is also good. I give it a good review.	",
            "The boss has a good attitude, fast delivery, and fast logistics. The most important thing is that the quality is very good and the packaging is tight. Thank you	",
            "The quality is good, and I will come to your house to buy in the future.	",
            "Not bad, you get what you pay for.	",
            "Super super cost-effective, good quality, fast speed, absolutely recommended	",
            "The item has been received, it is very good, I like it very much, give it a thumbs up.	",
            "Good, good, good, all in words	",
            "The goods are not bad, the style is good, but some details are still very general, not as good as expected.	",
            "Baby is good. The delivery is very fast!	",
            "The quality is very good, very good. I will come again next time.	",
            "  Great seller. Thank you. My colleagues all like it. Come again next time.	",
            "The delivery is fast, the items are good and effective.	",
            "The quality is very good, exactly as described by the seller, very satisfied, I really like it, completely exceeded expectations, the delivery speed is very fast, the packaging is very careful and strict, the service attitude of the logistics company is very good, the delivery speed is very fast, very satisfied Shopping.	",
            "After receiving the goods, I unpacked them as soon as possible. I feel that the quality is still relatively good. It is consistent with the description of the seller. I am very satisfied. The delivery speed is relatively fast. The logistics company has a good service attitude and the delivery speed is very fast. Generally speaking This is a very satisfying shopping, thanks to the seller.	",
            "The baby has been received, the seller delivered the goods quickly, and the logistics is also very good. The customer service attitude is excellent, and the patience gives people a sense of intimacy. I like it very much. There is also exquisite packaging, high-end atmosphere; it can be seen that the merchant is very careful. The baby is really good, it matches the picture, there is no difference, it is really worth the money.	",
            "I think it’s very good. When I bought it, I saw that the comments said it was good and I bought it. I was very excited when I saw the delivery. After it arrived, I was so excited that I took it back to the dormitory from the courier and tried it. Pretty good! And the customer service lady is also very nice and polite, the customer service lady also answered my questions in seconds, hehe, I will buy it again next time.	",
            "The goods have arrived, much better than what you see in the picture!	",
            "What a good seller. In the future, if there is any need for babies in this area, I will have to find you!	",
            "Not bad, top one, who wants you to be so honest. Oh thank you slightly!	",
            "This store is okay. I have bought here several times, and the service to old customers is very thoughtful, and I will come here often in the future!	",
            "Sincerely thank you for allowing me to buy the treasure I dreamed of, thank you so much!	",
            "After my personal experience, the reputation of this store is quite good. The quality of the baby is more like a diamond. Thank you so much!	",
            "Don't think that the quality of goods from sellers with low reputation is not good. I use my personal experience to tell everyone that the shopkeeper's service attitude is very good. The product quality is also very good. I love you!	",
            "  hehe. The product arrived so quickly, it's not bad, you can get a discount next time	",
            "I really didn't expect online shopping to be so interesting. Under the guidance of the seller, I finally learned how to shop online. Thank you!	",
            "This store is really good, the quality is cheap, the boss is very generous, and the customer service is also very patient. I don’t understand this aspect, but they patiently explained it to me. It is really the conscience of the industry. I will recommend it to me in the future. My friends this store, let them all come to this store to buy clothes!	",
            "I finally waited for the item I bought. At first I was still debating whether to buy it or not. I liked it a little bit, but I was a little scared after reading the reviews. However, they are all wrong, the item is good, it is genuine, the boss is very nice, and the logistics is good.	",
            "Good seller, I am a novice, I don’t understand a lot of things, but fortunately, the seller patiently guides me on the other end of the computer, I also have a sense of accomplishment, and finally understand the problem that I have not understood ^—^ Learn a lot!	",
            "The seller, MM, has a very high level and is very patient. I saw that many buyers gave very high evaluations. I didn’t expect that such a seller is really like this. It’s rare to see it online. I’ve asked many sellers. I can't do it, the seller MM has done it. And the price is also low. I'm really happy. The white style, my colleagues all said it is very beautiful, there is nothing picky about it, all five points.	",
            "I heard from my colleagues that they said the quality is good, and I will come to your house next time. hehe.	",
            "I feel that the service attitude is very good for the value for money’. I’m not picky. The five-point size is very correct. I like it very much. The workmanship is fine, the material is very good, and it is very comfortable to wear. I am satisfied with the shopping!	",
            "The products in your store are of very good quality. Thanks!	",
            "Great shoes, great service, thank you.	",
            "The seller is indeed very good, the baby is just as described	",
            "Received this morning, I like it very much, I will definitely come back next time.	",
            "The attitude of the logistics company is relatively poor, it is recommended to change to another one! But the store manager is not bad!	",
            "This is the second time I bought it. The goods are good. The boss is very nice.	",
            "The price/performance ratio is very high. The quality can be bought at such a price.	",
            "One word \"good\"!	",
            "It's really good to buy it for the third time!	",
            "Things are so cheap, earning credit is not bad, hehe, not bad baby is really good~	",
            "Friends say it's good, it's worth it!	",
            "I was a little worried at first, but I went to verify it. It is a licensed product and a good seller.	",
            "Unexpectedly, the boss changed it unconditionally, ha ha.	",
            "The store's goods are exquisite in workmanship, and the service is also good. Give it a good review.	",
            "Value for money.	",
            "It's pretty good, hehe, I will continue to patronize in the future.	",
            "The goods have arrived, the quality is very good, please support~	",
            "I'm an old regular customer, and the things are still as good as ever. The genuine Japanese goods are the last order, and the price performance is outstanding.	",
            "Sure enough, you get what you pay for, and the style is super good.	",
            "Thank you, I will buy from you again in the future.	",
            "The store owner was very patient and asked for three consecutive days before deciding to buy. I am very satisfied with the goods received.	",
            "Good seller, he is really top-notch, he is very considerate of the seller, I like this one.	",
            "Good seller, I will come again next time, thank you seller for your enthusiasm~	",
            "The actual color is slightly different from the picture, but I am quite satisfied.	",
            "Good seller, fast delivery!	",
            "I'm dizzy after choosing from east to west, so let's choose this one, the reviews are also good.	",
            "I feel that my eyesight is still good, very good-looking!	",
            "I am very satisfied after buying a lot of things. I am a very good seller. I will come here often. The discount card can be upgraded to the top level.	",
            "  very beautiful. Worth having~ I will come back next time~	",
            "  Quite good looking.	",
            "Hehe, it's very suitable as a gift for friends!	",
            "The quality is good, and I will come to your house to buy in the future.	",
            "I bought it for a relative, and the effect is not bad.	",
            "The delivery speed is very fast, the item is very good! !	",
            "The items are beautiful, and the seller's service attitude is very good. I will come again next time.	",
            "Very good seller, will continue to support you!	",
            "Haha, I like it very much. I will buy more when I see a good-looking bag.	",
            "The shoes I just wore are very good!	",
            "I'm busy, I've forgotten all about it, so I'm sorry, what else can I say~~ I'm lucky enough to meet the boss in this shop in my lifetime.	",
            "Not bad, not bad, I like such a boss~~~~	",
            "Small and exquisite, definitely worthy of the price! !	",
            "Very beautiful, thank you, the owner.	",
            "Supporting the seller, baby is really good.	",
            "The item has been received, it is very good, I like it very much, give it a thumbs up.	",
            "Although I haven't got it yet, my father said it was good!	",
            "Every time I trade with you, I am so successful and happy. I hope we will have more transactions in the future, haha.	",
            "The goods arrived, very consistent with the description, good seller!	",
            "Very good buyer. It is my honor to have a buyer like you. I hope to visit our store again next time! Thanks!	",
            "Not bad boss, next time I will buy something else	",
            "YY is good, the starting price is right!	",
            "The goods arrived very quickly, the quality is very good, a good store, I recommend it~	",
            "The goods have arrived, they are very beautiful~ good seller~	",
            "The shop is indeed very good, I have collected it~	",
            "The delivery is fast, the items are good and effective.	",
            "I've been too busy recently, so the confirmation was late, but the things are very good, haha.	",
            "The delivery was super fast, but I wasn't there and couldn't sign for it in time. Like Like.	",
            "Fortunately, I chose a larger size, super slim, great!	",
            "  good service! The gift is really beautiful and worth it! However, there is a bead with some flaws, which is inevitable. The size is smaller than expected. Overall not bad!	",
            "Much prettier than the pictures~	",
            "The quality is very good, very good!	",
            "Very good seller, absolutely support.	",
            "I have bought a lot of homes, and the software is very good!	",
            "I am a regular customer. I hope there will be more good things in the future.	"
        ];

        $package = SellerPackage::where(['is_default' => 1 ])->first();

        $bloc_id = 0;
        $staff_id = 0;
        $customerIds = User::query()->where('user_type', 'customer')->pluck('id')->toArray();

        // 排除已复制产品
        $alreadyCopyIds = Product::query()
            ->where('published', 1)
            ->whereNotNull('original_id')
            ->pluck('original_id')
            ->toArray();

        // 虚拟店铺，无集团分组的
        $shop_num = $this->argument('shop_num');
        if (empty($shop_num)) $shop_num = 300;

//        \DB::connection()->enableQueryLog();#开启执行日志
        $shops = Shop::query()->join("users", "users.id", "=", "shops.user_id")
            ->leftJoin("products", "products.user_id", "=", "shops.user_id")
            ->where('users.is_virtual_user', 1)
            ->where('shops.bloc_id', 0)
            ->where('shops.rating', '>=', 4)
            ->where('shops.num_of_reviews', '>=', 10)
            ->selectRaw("shops.*, count(products.id) as totalProduct")
            ->groupBy("shops.user_id")
            ->having("totalProduct", 0)
            ->limit($shop_num)
            ->get();
//        dd(\DB::getQueryLog());//打印SQL语句

        $products = Product::query()->where('in_storehouse', 1);

        if (!empty($alreadyCopyIds)) {
            $products = $products->whereNotIn('id', $alreadyCopyIds);
        }

        $category_ids = $this->argument('category_ids');
        if (is_null($category_ids)) {
            $this->info('确定不指定分类，将category_ids参数设置为0');
            return;
        }

        $this->info(var_export([$category_ids, !empty($category_ids)], true));
        if (!empty($category_ids)) {
            $category_ids = explode(",", $category_ids);
            $products = $products->whereIn('category_id', $category_ids);
        }
        $products = $products->select(['id', 'category_id'])->get()->toArray();
        $categoriesProductIds = [];
        foreach ($products as $product) {
            if (!isset($categoriesProductIds[$product['category_id']])) {
                $categoriesProductIds[$product['category_id']] = [];
            }
            $categoriesProductIds[$product['category_id']][] = $product['id'];
        }
        if (empty($categoriesProductIds)) {
            $this->info('分类下无数据');
            return;
        }

        $pb = $this->output->createProgressBar(count($shops));
        foreach ($shops as $shop) {
            if (empty($categoriesProductIds)) {
                $this->info('所有分类下产品已使用完毕或者不够20个产品');
                break;
            }

            $randCategoryId = array_rand($categoriesProductIds, 1);
            // 生成店铺
            $user = $shop->user;
            $limit = mt_rand(20, 40);
            $partProductIds = [];
            for ($i  = 0; $i < $limit; $i++) {
                $partProductIds[] = array_pop($categoriesProductIds[$randCategoryId]);
            }
            echo 'LIMIT=' . $limit . ':' . count($partProductIds) . PHP_EOL;

            // 不够20个产品的，直接去掉这个分类
            if (count($categoriesProductIds[$randCategoryId]) < 20) {
                unset($categoriesProductIds[$randCategoryId]);
            }

            // 上传产品
            $maxProfit = $package->max_profit / 100;
            foreach ($partProductIds as $productId) {
                $product = Product::find($productId);
                if (empty($product)) {
                    echo $productId . "不存在产品" . PHP_EOL;
                    continue;
                }
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
                $this->productStockService->product_duplicate_store($product->stocks, $product_new, $maxProfit);

                //VAT & Tax
                $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

                // 生成评论
//                echo 'contents:' . count($comments) . PHP_EOL;
                $rand_index = array_rand($comments, mt_rand(2, 5));
//                echo 'rand contents:' . count($rand_index) . PHP_EOL;
                foreach ( $rand_index as $index) {
                    $reviewUserIdIndex = array_rand($customerIds, 1);
                    $reviewModel = new Review;
                    $reviewModel->bloc_id = $bloc_id;
                    $reviewModel->staff_id = $staff_id;
                    $reviewModel->product_id = $product_new->id;
                    $reviewModel->user_id = $customerIds[$reviewUserIdIndex];
                    $reviewModel->rating = mt_rand(4, 5);
                    $reviewModel->comment = $comments[$index];
                    $reviewModel->viewed = '0';
                    $reviewModel->save();
                }
            }

            $pb->advance(1);
        }

        $pb->finish();
    }
}
