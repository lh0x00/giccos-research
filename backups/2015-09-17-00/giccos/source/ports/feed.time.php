<?php
header($_parameter->get('contentType_html.utf8'));
?>
<!DOCTYPE html>
<html lang="<?php print $g_client['language']['code']; ?>" id="giccos" class="feed time">
	<head>
		<title><?php if (isset($feed_['content']['title']) && $feed_['content']['title'] != null) { print $feed_['content']['title'].' :: '; } print $_language->text('pages_time.title', 'ucwords', false, false); ?></title>
		<script src="<?php print $_tool->links("source/js/jquery.js"); ?>" type="text/javascript"></script>
		<link href="<?php print $_tool->links("source/css/basic.css"); ?>" rel="stylesheet" />
		<script src="<?php print $_tool->links("source/js/basic.js"); ?>" type="text/javascript"></script>
		<link href="<?php print $_tool->links("source/css/feed.css"); ?>" rel="stylesheet" />
		<script src="<?php print $_tool->links("source/js/feed.js"); ?>" type="text/javascript"></script>
		<script src="<?php print $_tool->links("source/js/feed.general.js"); ?>" type="text/javascript"></script>
	</head>
	<body>
		<div id="gMain">
			<?php require_once ("templates/navbar.php"); ?>
			<div id="gBox">
				<div id="leftBox">
				<?php 
					$boxLinksRequire['mode'] = $boxLinksRequire['groups'] = $boxLinksRequire['hashtag'] = true;
					require_once ("templates/box_links.php"); 
				?>
				</div>
				<div id="centerBox">
					<?php
					require_once ("templates/box_ask.php");
					require_once ("templates/editor.php");
					$statusListOptions = array(
						"guy" => $g_user['mode'],
						"type" => "time",
						"method" => $feed_['options']['method'],
						"sort" => ">",
						"logs" => 0,
						"order" => "new",
						"limit" => 5,
						"time" => $feed_['options']['time']
					);
					$listOptionsArr = $statusListOptions;
					if (isset($listOptionsArr['guy'])) {
						unset($listOptionsArr['guy']);
					}
					$statusList = $_feed->status_list($statusListOptions);
					if (isset($statusList['return'], $statusList['count'], $statusList['data']) && $statusList['return'] == true) {
						$feedCode = null;
						if ($statusList['count'] == 0 || count($statusList['data']) == 0) {
							$feedCode = "<div class='null inFeeds boxGrid'> <div class='title'><span>{$_language->text('inFeeds_null_title_text', 'ucfirst')}</span></div> <div class='description'><span>{$_language->text('inFeeds_null_notify_text', 'ucfirst')}.</span></div> </div>";
						}else {
							$listStatusArr = array();
							foreach ($statusList['data'] as $listLabel => $listValue) {
								$listStatusArr[] = array_merge($listValue, array("media" => true, "comment" => true));
							}
							$statusFetch = $_feed->status_fetch(array("guy" => $g_user['mode'], "list" =>  $listStatusArr));
							if (isset($statusFetch['return'], $statusFetch['count'], $statusFetch['data']) && $statusFetch['return'] == true) {
								foreach ($statusFetch['data'] as $statusArrThis) {
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
						<div id='gFeeds' class='thisFeeds feed' feed='".json_encode($listOptionsArr)."'>
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
		</div>
		<?php require_once ("templates/sidebar.php"); ?>
		<div id="gGlobal"></div>
		<div id="gSource"></div>
	</body>
</html>