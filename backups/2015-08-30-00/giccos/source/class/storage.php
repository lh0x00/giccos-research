<?php
if (!defined('7c9a87aba3d96d2dc3edc45da062ce72')) {
    die(header('HTTP/1.0 404 Not Found'));
}
Class storage {
	function __construct () {
		$GLOBALS["_storage"] = $this;
		$this->class = $GLOBALS;
		$this->db = $this->class['_db']->port('beta');
	}
	function upload ($object) {
		$db = $this->db;
		$_parameter = $this->class['_parameter'];
		$_tool = $this->class['_tool'];
		if (isset($object['format']) && is_string($object['format'])) $format = $object['format']; else $format = null;
		if (!in_array($format, ["image", "audio", "video"])) {
			return array("return" => false, "reason" => "");
		}else {
			if (isset($object['file']) && is_string($object['file'])) $file = $object['file']; else $file = null;
			if (isset($object['url']) && is_string($object['url'])) $url = $object['url']; else $url = null;
			if ($url != null) {
				//.
			}else if ($file != null) {

			}
			if (!file_exists($file) && $file != "none") {
				return array("return" => false, "reason" => "");
			}
			if (isset($object['move']) && is_string($object['move'])) $move = $object['move']; else $move = null;
			if (isset($object['folder']) && is_string($object['folder'])) $folder = $object['folder']; else $folder = null;
			if (isset($object['port']) && is_string($object['port'])) $port = $object['port']; else $port = null;
			if (isset($object['name']) && is_string($object['name'])) $name = $object['name']; else $name = null;
			if (isset($object['mime']) && is_string($object['mime'])) $mime = $object['mime']; else $mime = null;
			if (isset($object['compression']) && is_bool($object['compression'])) $compression = $object['compression']; else $compression = true;
			if (isset($object['strip']) && is_bool($object['strip'])) $strip = $object['strip']; else $strip = true;
			$storage = array();
			$storage['time'] = time();
			$storage['mime'] = isset($mime) && is_string($mime) && $mime != null ? $mime : $_tool->mime_content_type($file);
			$storage['ext'] = isset($storage['ext']) && is_string($storage['ext']) && $storage['ext'] != null ? $_tool->mime_content_type($storage['mime'], true) : strtolower(pathinfo($file, PATHINFO_EXTENSION));
			$storage['folder'] = isset($folder) && (is_string($folder) || is_numeric($folder)) && $folder != null ? $folder : $_parameter->get('storage_folder_name');
			$storage['port'] = isset($port) && (is_string($port) || is_numeric($port)) && $port != null ? $port : rand($_parameter->get('storage_folder_min'), $_parameter->get('storage_folder_max'));
			if ($name != null) {
				$storage['name'] = $name;
				if (in_array($storage['ext'], [null, "tmp"])) {
					$storage['ext'] = strtolower(pathinfo($storage['name'], PATHINFO_EXTENSION));
				}
			}else {
				$storage['name'] = null;
			}
			$storage['name'] = $_tool->convertDatabaseString($storage['name']);
			$storage['nameraw'] = hash("md5", $storage['folder'].'::'.$storage['port'].'::'.$storage['time'].'::'.rand().'_'.rand().'_'.rand());
			if (isset($storage['ext']) && is_string($storage['ext']) && $storage['ext'] != null) {
				if ($file == "none") {
					$storage['nameraw'] .= '.'.$storage['ext'];
				}else {
					$storage['nameraw'] .= '.'.$storage['ext'];
				}
			}
			$storage['token'] = hash("crc32", $storage['nameraw']);
			$storage['display'] = hash("crc32", $storage['token']);
			$storage['secret'] = null;
			if ($file == "none") {
				$storage['hash'] = 0;
				$storage['size'] = 0;
			}else {
				$storage['hash'] = md5_file($file);
				$storage['size'] = filesize($file);
			}
			$storage['name'] = $storage['name'];
			$storage['status'] = time();
			$storage['dir'] = $_parameter->get('storage_folder_path').'/'.$format.'/'.$storage['folder'].'/'.$storage['port'];
			if (!file_exists($storage['dir'])) { 
				mkdir($storage['dir']);
				chmod($storage['dir'], 0777);
			}
			$storage['source'] = $storage['dir'].'/'.$storage['nameraw'];
			if (file_exists($storage['source'])) {
				$storage['nameraw'] = hash("md5", $storage['folder'].'::'.$storage['port'].'::'.$storage['time'].'::'.rand().'_'.rand().'_'.rand());
				if ($file == "none") {
					$storage['nameraw'] .= '.'.$storage['ext'];
				}else {
					$storage['nameraw'] .= '.'.$storage['ext'];
				}
				$storage['token'] = hash("crc32", $storage['nameraw']);
				$storage['display'] = hash("crc32", $storage['token']);
				$storage['dir'] = $_parameter->get('storage_folder_path').'/'.$format.'/'.$storage['folder'].'/'.$storage['port'];
				if (!file_exists($storage['dir'])) { 
					mkdir($storage['dir']);
					chmod($storage['dir'], 0777);
				}
				$storage['source'] = $storage['dir'].'/'.$storage['nameraw'];
			}
			$storage['path'] = $_tool->hash("encode", $storage['source'], $storage['token'].'::'.$_parameter->get('hash_storage_path'));
			$sql = "INSERT INTO `storage_{$format}` (`id`, `folder`, `port`, `time`, `token`, `secret`, `hash`, `path`, `mime`, `display`, `name`, `status`, `size`) VALUES (NULL, '{$storage['folder']}', '{$storage['port']}', '{$storage['time']}', '{$storage['token']}', '{$storage['secret']}', '{$storage['hash']}', '{$storage['path']}', '{$storage['mime']}', '{$storage['display']}', '{$storage['name']}', '{$storage['status']}', '{$storage['size']}');";
			$query = mysqli_query($db, $sql);
			if ($move == "rename") {
				$puts = $_tool->rename($file, $storage['source']);
			}else if ($move == "copy") {
				$puts = $_tool->copy($file, $storage['source']);
			}else if ($move == "none") {
				$puts = true;
			}else {
				return array("return" => false, "reason" => "");
			}
			if (!$query || !$puts) {
				return array("return" => false, "reason" => "");
			}else {
				$storage['id'] = mysqli_insert_id($db);
				if ($format == "image") {
					if ($file != "none" && $compression == true) {
						$_tool->exec("convert {$storage['source']} -quality {$_parameter->get('photos_compression_quality_percent')}% {$storage['source']}");
					}
					if ($file != "none" && $strip == true) {
						$storage['icc']['dir'] = $_parameter->get('storage_folder_path')."/image/icc/".$storage['port'];
						if (!file_exists($storage['icc']['dir'])) { 
							mkdir($storage['icc']['dir']);
							chmod($storage['icc']['dir'], 0777);
						}
						$storage['icc']['nameraw'] = preg_replace("/(\.{$storage['ext']})$/", ".icm", $storage['nameraw']);
						$storage['icc']['source'] = $storage['icc']['dir'].'/'.$storage['icc']['nameraw'];
						$_tool->exec("convert {$storage['source']} {$storage['icc']['source']}");
						$_tool->exec("convert {$storage['source']} -strip -profile {$storage['icc']['source']} -interlace Plane -gaussian-blur 0.05 {$storage['source']}");
						$_tool->unlink($storage['icc']['source']);
					}
				}else {
					//.
				}
			}
			return array("return" => true, "storage" => $storage);
		}
	}
	function get ($object) {
		$db = $this->class['_db']->port('beta');
		$_parameter = $this->class['_parameter'];
		$_tool = $this->class['_tool'];
		//.
		if (isset($object['format']) && is_string($object['format'])) $format = $object['format']; else $format = null;
		if (isset($object['label']) && is_string($object['label'])) $label = $object['label']; else $label = null;
		if (isset($object['value']) && is_string($object['value'])) $value = $object['value']; else $value = null;
		if (isset($object['limit']) && is_string($object['limit'])) $limit = $object['limit']; else $limit = null;
		if ($label == null || $value == null) {
			return array("return" => false, "reason" => "");
		}
		if ($format == "image") {
			$getRequest = "SELECT * FROM `storage_image` WHERE `".$label."` = '".$value."' ".$limit.";";
		}else if ($format == "audio") {
			$getRequest = "SELECT * FROM `storage_audio` WHERE `".$label."` = '".$value."' ".$limit.";";
		}else if ($format == "video") {
			$getRequest = "SELECT * FROM `storage_video` WHERE `".$label."` = '".$value."' ".$limit.";";
		}else {
			return array("return" => false, "reason" => "");
		}
		if (!isset($getRequest)) {
			return array("return" => false, "reason" => "");
		}
		$getQuery = mysqli_query($db, $getRequest);
		if (!$getQuery) {

		}
		$fileArr = array();
		while ($getFetch = mysqli_fetch_assoc($getQuery)) {
			if (isset($getFetch['path'], $getFetch['token'])) {
				$getFetch['source'] = $_tool->hash('decode', $getFetch['path'], $getFetch['token'].'::'.$_parameter->get('hash_storage_path'));
			}
			$fileArr[] = $getFetch;
		}
		$fileCount = count($fileArr);
		return array("return" => true, "count" => $fileCount, "data" => $fileArr);
	}
	function delete ($object) {
		$_parameter = $this->class['_parameter'];
		$_tool = $this->class['_tool'];
		$db = $this->class['_db']->port('beta');
		//.
		if (isset($object['format']) && is_string($object['format'])) $format = $object['format']; else $format = null;
		if (isset($object['label']) && (is_string($object['label']) || is_numeric($object['label']))) $label = $object['label']; else $label = null;
		if (isset($object['value']) && (is_string($object['value']) || is_numeric($object['value']))) $value = $object['value']; else $value = null;
		if (isset($object['limit']) && is_string($object['limit'])) $limit = $object['limit']; else $limit = null;
		if ($label == null || $value == null) {
			return array("return" => false, "reason" => "");
		}
		if (!in_array($format, ["image", "audio", "video"])) {
			return array("return" => false, "reason" => "");
		}
		$selectRequest = "SELECT * FROM `storage_{$format}` WHERE `{$label}` = '{$value}' {$limit}";
		$selectQuery = mysqli_query($db, $selectRequest);
		if (!$selectQuery) {
			return array("return" => false, "reason" => "");
		}
		if (mysqli_num_rows($selectQuery) == 0) {
			return array("return" => true, "num" => 0, "data" => array());
		}else {
			$notifyArr = array();
			while ($dataArr = mysqli_fetch_assoc($selectQuery)) {
				$file = $_tool->hash("decode", $dataArr['path'], $dataArr['token'].'::'.$_parameter->get('hash_storage_path'));
				if (@file_exists($file)) {
					$removeFile = $_tool->unlink($file);
				}else {
					$removeFile = true;
				}
				if (isset($removeFile) && $removeFile == true) {
					if ($format == "image") {
						if (isset($dataArr['icc']) && $dataArr['icc'] != null) {
							$iccFile = $_tool->hash("decode", $dataArr['icc'], $dataArr['token'].'::'.$_parameter->get('hash_storage_path'));
							if (@file_exists($iccFile)) {
								$_tool->unlink($iccFile);
							}else {
								//.
							}
						}
					}
					$deleteRequest = "DELETE FROM `storage_{$format}` WHERE `id` = '{$dataArr['id']}'";
					$deleteQuery = mysqli_query($db, $deleteRequest);
					if (!$deleteQuery) {
						$notifyArr[] = array("return" => false, "reason" => "");
					}else {
						$notifyArr[] = array("return" => true);
					}
				}else {
					$notifyArr[] = array("return" => false, "reason" => "");
				}
			}
			return array("return" => true, "num" => count($notifyArr), "data" => $notifyArr);
		}
	}
	function replace ($object) {
		$db = $this->class['_db']->port('beta');
		$_parameter = $this->class['_parameter'];
		$_tool = $this->class['_tool'];
		if (isset($object['format']) && is_string($object['format'])) $format = $object['format']; else $format = null;
		if (isset($object['labelSource']) && is_string($object['labelSource'])) $labelSource = $object['labelSource']; else $labelSource = null;
		if (isset($object['valueSource']) && is_string($object['valueSource'])) $valueSource = $object['valueSource']; else $valueSource = null;
		if (isset($object['labelDestination']) && is_string($object['labelDestination'])) $labelDestination = $object['labelDestination']; else $labelDestination = null;
		if (isset($object['valueDestination']) && is_string($object['valueDestination'])) $valueDestination = $object['valueDestination']; else $valueDestination = null;
		if (!in_array($format, ["image", "audio", "video"])) {
			return array("return" => false, "reason" => "");
		}
		$getMediaFileSource = $this->get(array("format" => $format, "label" => $labelSource, "value" => $valueSource, "limit" => "LIMIT 1"));
		if (isset($getMediaFileSource['return'], $getMediaFileSource['data'], $getMediaFileDestination['file'][0]) && $getMediaFileSource['return'] == true) {
			$dataMediaFileSourceData = $getMediaFileSource['data'][0];
			$dataMediaFileSource['display'] = $dataMediaFileSourceData['display'];
			$dataMediaFileSource['source'] = $_tool->hash("decode", $dataMediaFileSourceData['path'], $dataMediaFileSourceData['token'].'::'.$_parameter->get('hash_storage_path'));
		}else {
			return array("return" => false, "reason" => "");
		}
		$getMediaFileDestination = $this->get(array("format" => $format, "label" => $labelDestination, "value" => $valueDestination, "limit" => "LIMIT 1"));
		if (isset($getMediaFileDestination['return'], $getMediaFileDestination['data'], $getMediaFileDestination['file'][0]) && $getMediaFileDestination['return'] == true) {
			$dataMediaFileDestinationData = $getMediaFileDestination['data'][0];
			$dataMediaFileDestination['display'] = $dataMediaFileDestinationData['display'];
			$dataMediaFileDestination['source'] = $_tool->hash("decode", $dataMediaFileDestinationData['path'], $dataMediaFileDestinationData['token'].'::'.$_parameter->get('hash_storage_path'));
		}else {
			return array("return" => false, "reason" => "");
		}
		if (!isset($dataMediaFileSource) || !isset($dataMediaFileDestination)) {
			return array("return" => false, "reason" => "");
		}else {
			if (!file_exists($dataMediaFileSource['file'])) {
				$this->recheck(array("format" => $format, "label" => $labelSource, "value" => $valueSource));
				return array("return" => false, "reason" => "");
			}
		}
		$replaceDestinationFile = $_tool->copy($dataMediaFileSource['source'], $dataMediaFileDestination['source']);
		$updateDestinationSql = "UPDATE `storage_{$format}` SET `secret` = '{$dataMediaFileSource['secret']}', `hash` = '{$dataMediaFileSource['hash']}', `mime` = '{$dataMediaFileSource['mime']}', `name` = '{$dataMediaFileSource['name']}', `status` = '{$dataMediaFileDestination['status']}', `size` = '{$dataMediaFileDestination['size']}' WHERE `id` = '{$dataMediaFileDestination['id']}'";
		$updateDestinationQuery = mysqli_query($db, $updateDestinationSql);
		if (!$replaceDestinationFile || !$updateDestinationQuery) {
			return array("return" => false, "reason" => "");
		}else {
			$dataMediaFileDestination['secret'] = $dataMediaFileSource['secret'];
			$dataMediaFileDestination['hash'] = $dataMediaFileSource['hash'];
			$dataMediaFileDestination['mime'] = $dataMediaFileSource['mime'];
			$dataMediaFileDestination['name'] = $dataMediaFileSource['name'];
			$dataMediaFileDestination['status'] = $dataMediaFileSource['status'];
			$dataMediaFileDestination['size'] = $dataMediaFileSource['size'];
			return array("return" => true, "source" => $dataMediaFileSource, "destination" => $dataMediaFileDestination);
		}
	}
	function recheck ($object) {
		$db = $this->class['_db']->port('beta');
		$_parameter = $this->class['_parameter'];
		$_tool = $this->class['_tool'];
		if (isset($object['format']) && is_string($object['format'])) $format = $object['format']; else $format = null;
		if (isset($object['label']) && is_string($object['label'])) $label = $object['label']; else $label = null;
		if (isset($object['value']) && is_string($object['value'])) $value = $object['value']; else $value = null;
		if (!in_array($format, ["image", "audio", "video"])) {
			return array("return" => false, "reason" => "asd");
		}
		$getMediaFile = $this->get(array("format" => $format, "label" => $label, "value" => $value, "limit" => "LIMIT 1"));
		if (isset($getMediaFile['return'], $getMediaFile['data'], $getMediaFile['data'][0]) && $getMediaFile['return'] == true) {
			$dataMediaFile = $getMediaFile['data'][0];
			$dataMediaFile['source'] = $dataMediaFile['source'];
			if (!file_exists($dataMediaFile['source'])) {
				$updateMediaFileRequest = "UPDATE `storage_{$format}` SET `status` = '0', `size` = '0' WHERE `id` = '{$dataMediaFile['id']}'";
				$updateMediaFile = mysqli_query($db, $updateMediaFileRequest);
				if (!isset($updateMediaFile)) {
					return array("return" => false, "reason" => "dfa");
				}else {
					$dataMediaFile['status'] = 0;
					return array("return" => true, "data" => $dataMediaFile);
				}
			}else {
				$dataMediaFile['hash'] = md5_file($dataMediaFile['source']);
				$dataMediaFile['mime'] = $_tool->mime_content_type($dataMediaFile['source']);
				$dataMediaFile['status'] = time();
				$dataMediaFile['size'] = filesize($dataMediaFile['source']);
				$updateMediaFileRequest = "UPDATE `storage_{$format}` SET `hash` = '{$dataMediaFile['hash']}', `mime` = '{$dataMediaFile['mime']}', `status` = '{$dataMediaFile['status']}', `size` = '{$dataMediaFile['size']}' WHERE `id` = '{$dataMediaFile['id']}'";
				$updateMediaFile = mysqli_query($db, $updateMediaFileRequest);
				if (!isset($updateMediaFile)) {
					return array("return" => false, "reason" => "sasddf");
				}else {
					return array("return" => true, "data" => $dataMediaFile, "s" => $updateMediaFileRequest);
				}
			}
		}else {
			return array("return" => false, "reason" => "sdfg", "sd" => $getMediaFile);
		}
	}
	function exists ($object) {
		$db = $this->class['_db']->port('beta');
		$_parameter = $this->class['_parameter'];
		$_tool = $this->class['_tool'];
		if (isset($object['format']) && is_string($object['format'])) $format = $object['format']; else $format = null;
		if (isset($object['label']) && is_string($object['label'])) $label = $object['label']; else $label = null;
		if (isset($object['value']) && is_string($object['value'])) $value = $object['value']; else $value = null;
		if (isset($object['limit']) && is_string($object['limit'])) $limit = $object['limit']; else $limit = null;
		if ($label == null || $value == null) {
			return array("return" => false, "reason" => "");
		}
		if ($format == "image") {
			$getRequest = "SELECT `id` FROM `storage_image` WHERE `{$label}` = '{$value}' {$limit}";
		}else if ($format == "audio") {
			$getRequest = "SELECT `id` FROM `storage_audio` WHERE `{$label}` = '{$value}' {$limit}";
		}else if ($format == "video") {
			$getRequest = "SELECT `id` FROM `storage_video` WHERE `{$label}` = '{$value}' {$limit}";
		}else {
			return array("return" => false, "reason" => "");
		}
		if (!isset($getRequest)) {
			return array("return" => false, "reason" => "");
		}
		$getQuery = mysqli_query($db, $getRequest);
		if ($getQuery == true && mysqli_num_rows($getQuery) == 0) {
			return array("return" => true, "exists" => false, "count" => 0);
		}else if ($getQuery == true && mysqli_num_rows($getQuery) > 0) {
			return array("return" => true, "exists" => true, "count" => mysqli_num_rows($getQuery));
		}else {
			return array("return" => false, "reason" => "");
		}
	}
}
?>