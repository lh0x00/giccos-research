<?php
header($_parameter->get('contentType_html.utf8'));
/*
unset($_SESSION['cache']['weather']['8cef873a']);
print_r($_tool->getWeather(array("server" => "yahoo", "location" => "Ganh Hao", "units" => "C")));
print_r($_SESSION['cache']['weather']);
die;
*/
/*
print_r($g_user);
die;
print_r($_ip->info(array("action" => "check", "ip" => $_client->ip())));
die;
$ss = $_maps->places(array("guy" => $g_user['mode'], "action" => "get", "label" => "id", "value" => "1", "limit" => "LIMIT 1"));
print_r($ss);
die;
*/
//. print_r(json_decode('{"coord":{"lon":105.72,"lat":9.29},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02n"}],"base":"stations","main":{"temp":302.558,"pressure":1025.21,"humidity":91,"temp_min":302.558,"temp_max":302.558,"sea_level":1025.37,"grnd_level":1025.21},"wind":{"speed":3.6,"deg":239.01},"clouds":{"all":24},"dt":1441453083,"sys":{"message":0.0151,"country":"VN","sunrise":1441406882,"sunset":1441451013},"id":1591474,"name":"Thanh pho Bac Lieu","cod":200}', true));
//. die;
?>
<!DOCTYPE html>
<html lang="<?php print $g_client['language']['code']; ?>" id="giccos" class="feed general">
	<head>
		<title><?php print $_language->text('pages_feed_general.title', 'ucwords'); ?></title>
		<script src="<?php print $_tool->links("source/js/jquery.js"); ?>" type="text/javascript"></script>
		<link href="<?php print $_tool->links("source/css/basic.css"); ?>" rel="stylesheet" />
		<script src="<?php print $_tool->links("source/js/basic.js"); ?>" type="text/javascript"></script>
		<link href="<?php print $_tool->links("source/css/feed.css"); ?>" rel="stylesheet" />
		<script src="<?php print $_tool->links("source/js/feed.js"); ?>" type="text/javascript"></script>
		<script src="<?php print $_tool->links("source/js/feed.general.js"); ?>" type="text/javascript"></script>
	</head>
	<body>
		<div id="gSource">
			<link href="<?php print $_tool->links("source/css/templates/mediaplayer.css"); ?>" rel="stylesheet" />
			<script src="<?php print $_tool->links("source/js/templates/mediaplayer.js"); ?>" type="text/javascript"></script>
			<link href="<?php print $_tool->links("source/css/templates/feed.css"); ?>" rel="stylesheet" />
			<script src="<?php print $_tool->links("source/js/templates/feed.js"); ?>" type="text/javascript"></script>
		</div>
		<div id="gMain">
			<?php require_once ("templates/navbar.php"); ?>
			<div id="gBox">
				<div id="leftBox">
					<?php require_once ("templates/box_links.php"); ?>
				</div>
				<div id="centerBox">
					<?php
					require_once ("templates/box_ask.php");
					require_once ("templates/editor.php");
					$statusListOptions = array(
						"guy" => $g_user['mode'],
						"type" => "general",
						"method" => $feed_['options']['method'],
						"sort" => ">",
						"id" => 0,
						"order" => "new",
						"limit" => 5
					);
					$statusList = $_feed->status_list($statusListOptions);
					if (isset($statusList['return'], $statusList['count'], $statusList['data']) && $statusList['return'] == true) {
						$feedCode = null;
						if ($statusList['count'] == 0 || count($statusList['data']) == 0) {
							$feedCode = "<div class='null inFeeds boxGrid'> <div class='title'><span>{$_language->text('inFeeds_null_title_text', 'ucfirst')}</span></div> <div class='description'><span>{$_language->text('inFeeds_null_notify_text', 'ucfirst')}.</span></div> </div>";
						}else {
							$listStatusId = array();
							foreach ($statusList['data'] as $key => $idThis) {
								$listStatusId[$idThis] = array("media" => true, "comment" => true);
							}
							$statusFetch = $_feed->status_fetch(array("guy" => $g_user['mode'], "id" =>  $listStatusId));
							if (isset($statusFetch['return'], $statusFetch['count'], $statusFetch['data']) && $statusFetch['return'] == true) {
								foreach ($statusFetch['data'] as $i => $statusArrThis) {
									$statusPrintCode = $_feed->status_printcode(array("guy" => $g_user['mode'], "status" => $statusArrThis, "classname" => "status inFeeds"));
									if (isset($statusPrintCode['return'], $statusPrintCode['code']) && $statusPrintCode['return'] == true) {
										$feedCode .= $statusPrintCode['code'];
									}
								}
							}else {
								//.
							}
						}
						$feedCode = "
						<div id='gFeeds' class='thisFeeds feed' feed='".json_encode($feed_['options'])."'>
							<div class='info inFeeds boxGrid' class='thisTotal'>
								<div class='title'> <span>".$_language->text('news_feed', 'ucfirst').": ..</span> </div>
								<div class='description'> <span>".$_language->text('pages_feed.quickdescription', 'ucfirst').".</span> </div>
							</div>
							".$feedCode."
						</div>
						";
						print $feedCode;
					}else {
						//.
					}
					?>
				</div>
				<div id="rightBox">
					<?php $boxSuggestRequire['weather'] = true; require_once ("templates/box_suggest.php"); ?>
				</div>
			</div>
			<?php require_once ("templates/sidebar.php"); ?>
		</div>
		<div id="gInclude"></div>
	</body>
</html>