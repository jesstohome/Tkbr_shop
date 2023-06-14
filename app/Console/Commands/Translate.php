<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Console\Command;

class Translate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:run {code?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('hello my boby');

        $url='https://translation.googleapis.com/language/translate/v2/?key=' . env('GOOGLE_TRANSLATE_KEY');

        /*$values = [
            [
                'lang' => 'en',
                'lang_key' => 'reviewed',
                'lang_value' => 'Reviewed',
            ]
        ];*/
        $values = Translation::query()->where(['lang' => 'en'])->whereNotIn('lang_key', ['paystack'])->get();
        $langs = Language::where('status', 1);
        if (!empty($this->argument('code'))) {
            $langs = $langs->where('code', $this->argument('code'));
        }
        $langs = $langs->get();

        $pb = $this->output->createProgressBar(count($values) * count($langs));

        foreach ($langs as $lang) {
            if ($lang['code'] == 'en') {
                $pb->advance(1);
                continue;
            };

            $translation_locale = Translation::where('lang', $lang['code'])->pluck('lang_value', 'lang_key')->toArray();
            foreach ($values as $value) {
                $pb->advance(1);
                $lang_key = $value['lang_key'];
                if (isset($translation_locale[$lang_key])) continue;

                //初始化
                $curl = curl_init();

                if (env('APP_ENV', 'production') == 'local') {
                    curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1");
                    curl_setopt($curl, CURLOPT_PROXYPORT, "19180");
                    curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);  // CURLPROXY_SOCKS5
                }

                $headers = [
                    'referer:*.littleshopstudio.com',
                ];

                if ($headers) {
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                }

                //设置抓取的url
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                //设置头文件的信息作为数据流输出
                curl_setopt($curl, CURLOPT_HEADER, 1);
                //设置获取的信息以文件流的形式返回，而不是直接输出。
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                //设置post方式提交
                curl_setopt($curl, CURLOPT_POST, 1);
                //设置post数据

                $target_code = $lang['code'] == 'hk' ? 'zh-TW' : $lang['app_lang_code'];
                $post_data = array(
                    "q" => str_replace(["\n", "\r", "\r\n", "\n\r"], '<br1>', $value['lang_value']),
                    "source" => 'en',
                    "target" => $target_code,
                    'format' => 'html'
                );
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                //执行命令
                $exec_data = curl_exec($curl);
//                dd($exec_data, curl_error($curl));

                //关闭URL请求
                curl_close($curl);

                //显示获得的数据
                $result=json_decode(strstr($exec_data,'{'),true);
//                dd($post_data, $result);
                $translatedText=$result['data']['translations'][0]['translatedText'];
                $translatedText=str_replace('＆QUOT;','"',$translatedText);
                $translatedText=str_replace('＆nbsp;','',$translatedText);
                $translatedText=str_replace('＆amp; nbsp;','',$translatedText);
                file_put_contents(storage_path('logs/translate.log'), $target_code . ' ===> '. $translatedText . PHP_EOL, FILE_APPEND);

                $translation_def = new Translation;
                $translation_def->lang = $lang['code'];
                $translation_def->lang_key = $lang_key;
                $translation_def->lang_value = preg_replace("/(<br1>)+/", "\n", $translatedText);
                $translation_def->save();
            }

            \Cache::forget('translations-' . $lang['code']);
        }

        $pb->finish();
    }
}
