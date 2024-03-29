<?php
if (!defined('7c9a87aba3d96d2dc3edc45da062ce72')) {
    die(header('HTTP/1.0 404 Not Found'));
}
Class giccos {
	function __construct () {
		$GLOBALS["_giccos"] = $this;
		$this->class = $GLOBALS;
	}
	function logsAjax () {
		return true;
	}
	function logsToken () {
		return true;
	}
	function getHTTP ($reset = false) {
		if ($reset == true || (!isset($_SESSION["client"]['http']) || !isset($_SESSION["client"]['http']['secure']))) {
			$_SESSION["client"]['http']['secure'] = false;
			return true;
		}
	}
	function token ($reset = false) {
		if ($reset == true || !isset($_SESSION["client"]['token'])) {
			//.
			$token['robot'] = md5('robot::'.time().'::'.rand());
			$token['ajax'] = md5('ajax::'.time().'::'.rand());
			//.
			$token['source']['url'] = hash('crc32', md5('source::url::'.time().'::'.rand()));
			$token['source']['css'] = hash('crc32', md5('source::css::'.time().'::'.rand()));
			$token['source']['js'] = hash('crc32', md5('source::js::'.time().'::'.rand()));
			$token['source']['xml'] = hash('crc32', md5('source::xml::'.time().'::'.rand()));
			$token['source']['ajax'] = hash('crc32', md5('source::ajax::'.time().'::'.rand()));
			$token['source']['font'] = hash('crc32', md5('source::font::'.time().'::'.rand()));
			$token['source']['file'] = hash('crc32', md5('source::file::'.time().'::'.rand()));
			//.
			$token['maps']['places'] = hash('crc32', md5('maps::places::'.time().'::'.rand()));
			//.
			$token['accounts']['register'] = hash('crc32', md5('accounts::register::'.time().'::'.rand()));
			$token['accounts']['login'] = hash('crc32', md5('accounts::login::'.time().'::'.rand()));
			//.
			$token['action']['user'] = hash('crc32', md5('action::user::'.time().'::'.rand()));
			$token['action']['status'] = hash('crc32', md5('action::status::'.time().'::'.rand()));
			$token['action']['sites'] = hash('crc32', md5('action::sites::'.time().'::'.rand()));
			$token['action']['maps'] = hash('crc32', md5('action::maps::'.time().'::'.rand()));
			$token['action']['photos'] = hash('crc32', md5('action::photos::'.time().'::'.rand()));
			$token['action']['music'] = hash('crc32', md5('action::music::'.time().'::'.rand()));
			$token['action']['videos'] = hash('crc32', md5('action::videos::'.time().'::'.rand()));
			$token['action']['cited'] = hash('crc32', md5('action::cited::'.time().'::'.rand()));
			$token['action']['messages'] = hash('crc32', md5('action::messages::'.time().'::'.rand()));
			$token['action']['explorer'] = hash('crc32', md5('action::explorer::'.time().'::'.rand()));
			$token['action']['ajaxify'] = hash('crc32', md5('action::ajaxify::'.time().'::'.rand()));
			//.
			$token['feed']['status'] = hash('crc32', md5('feed::status::'.time().'::'.rand()));
			$token['feed']['comment'] = hash('crc32', md5('feed::comment::'.time().'::'.rand()));
			//.
			$token['cache']['photos'] = hash('crc32', md5('cache::photos::'.time().'::'.rand()));
			$token['cache']['music'] = hash('crc32', md5('cache::music::'.time().'::'.rand()));
			$token['cache']['videos'] = hash('crc32', md5('cache::videos::'.time().'::'.rand()));
			$token['cache']['cited'] = hash('crc32', md5('cache::cited::'.time().'::'.rand()));
			//.
			$token['wall']['info'] = hash('crc32', md5('wall::info::'.time().'::'.rand()));
			$token['wall']['timeline'] = hash('crc32', md5('wall::timeline::'.time().'::'.rand()));
			$token['wall']['friends'] = hash('crc32', md5('wall::friends::'.time().'::'.rand()));
			//.
			$_SESSION["client"]['token'] = $token;
			$this->logsToken();
			return true;
		}
	}
	function clientId ($reset = false) {
		if ($reset == true || !isset($_SESSION["client"]['token'])) {
			//
			$_SESSION["client"]['id'] = $this->class['_client']->cientId();
			return true;
		}
	}
	function port () {
		$this->port = array(
			null, # 0.
			"", # 1.
			"feed", # 2.
			"status",  # 3.
			"hashtag", # 4.
			"groups", # 5.
			"wall", # 6.
			"friends", # 7.
			"messages", # 8.
			"notifications", # 9.
			"history", # 10.
			"settings", # 11.
			"accounts", # 12.
			"search", # 13.
			"photos", # 14.
			"videos", # 15.
			"music", #16
			"maps", #17
			"oops", #18
			"sites", #19
		);
		return $this->port;
	}
}
?>