<?php

class ParallelLinkExtension extends DataExtension {
	
	private static $update_js 				= true;
	private static $update_css 				= true;
	private static $update_images 			= true;
	private static $use_full_server_domain 	= false;
	private static $enabled 				= true;
	private static $servers 				= array();
	
	public static function config() {
		return Config::inst()->forClass(get_called_class());
	}
	
	public function modifyResponse($result){
		if(!$this->config()->enabled){
			return $result;
		}
		
		if($result instanceof HTMLText){
			$html = $result->getValue();
			
			if(Config::inst()->get('ParallelLinkExtension','update_js')){
				$html = $this->updateJS($html);
			}
			if(Config::inst()->get('ParallelLinkExtension','update_css')){
				$html = $this->updateCSS($html);
			}
			if(Config::inst()->get('ParallelLinkExtension','update_images')){
				$html = $this->updateImages($html);
			}
			$result->setValue($html);
		}

		return $result;
	}
	
	private function updateJS($html){
		preg_match_all("/<script([^>]*)src=\"(\/(themes|mysite)\/[^\"]*)\"/i",$html,$matches);
		
		foreach($matches[0] as $key => $needle){
			$domain = $this->getServer($needle) ;
			$type 	= isset($matches[1][$key]) ? $matches[1][$key] : "";
			$src 	= isset($matches[2][$key]) ? $domain . $matches[2][$key] : $pref;
			$repl	= "<script". $type ."src=\"$src\"";
			$html 	= str_ireplace($needle,$repl,$html);
		}
		
		return $html;
	}
	
	private function updateCSS($html){
		preg_match_all("/<link([^>]*)href=\"([^\"]*)\"/i",$html,$matches);
		
		foreach($matches[2] as $key => $url){
			
			if(strpos($url, '//') === FALSE){
				$domain = $this->getServer($url);
				$repl 	= $domain . (($url[0] != "/") ? '/' : '') . $url;
				$html 	= str_ireplace($url,$repl,$html);
			}
		}
	
		return $html;
	}
	
	private function updateImages($html){
		preg_match_all("/<img([^>]*)src=\"(\/assets\/[^\"]*)\"/i",$html,$matches);
		
		foreach($matches[0] as $key => $needle){
			$domain = $this->getServer($needle) ;
			$stuff 	= isset($matches[1][$key]) ? $matches[1][$key] : "";
			$src 	= isset($matches[2][$key]) ? $domain . $matches[2][$key] : $pref;
			$repl	= "<img". $stuff ."src=\"$src\"";
			$html 	= str_ireplace($needle,$repl,$html);
		}
		
		return $html;
	}
	
	private function getServer($key){
		$bit = $this->config()->servers[(crc32($key)&0xff) % count($this->config()->servers)];
		if(!$this->config()->use_full_server_domain){
			$bit = Director::protocol() . "$bit." . $_SERVER['HTTP_HOST'];
		}
		
		return $bit;
	}
	
}
