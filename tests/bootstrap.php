<?php 


//psr-0 autoloader
foreach(array(
		"Goetas\\Twital\\"=>__DIR__."/../src/",
		"goetas\\xml\\"=>__DIR__."/../../xmldom/src/",	
		//"goetas\\atal\\"=>__DIR__."/../src/",
		) as $ns => $dir){

	spl_autoload_register ( function($cname)use($ns, $dir){
		if(strpos($cname,$ns)===0){
			$path = $dir.strtr($cname, "\\","/").".php";
			require_once ($path);
		}
	});
}
//psr-0 autoloader
foreach(array(
		"Twig_"=>(__DIR__."/../../twig/lib/"),

		//"goetas\\atal\\"=>__DIR__."/../src/",
) as $ns => $dir){

	spl_autoload_register ( function($cname)use($ns, $dir){
		if(strpos($cname,$ns)===0){
			$path = $dir.strtr($cname, "\\_","//").".php";
			require_once ($path);
		}
	});
}

$loader = new Twig_Loader_Filesystem(array(__DIR__."/suite/templates"));


$tal = new \Goetas\Twital\Twital($loader);
echo $tal->getSource("include.xml");
echo "\n\n";
//echo $tal->compile(__DIR__."/suite/templates/foreach.xml");

