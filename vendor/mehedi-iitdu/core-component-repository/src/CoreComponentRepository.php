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