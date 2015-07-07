<?php

class ItemDB {
	public static $itemIdToName = array();
	public static $itemNameToId = array();
	public static $items = array();

	public static function __init() {
		$file = file_get_contents(Core::$basePath.'/data/Item-ids.txt');
		$lines = explode("\n", $file);
		foreach ($lines as $line) {
			$split = explode(':', $line);
			if ($split[0] == 0)
				continue;

			$name = preg_replace("/[^a-z0-9 ]+/", '', strtolower($split[1]));

			ItemDB::$itemIdToName[intval($split[0])] = $name;
			$name2 = $name;
			$id2 = 1;

			while (array_key_exists($name2.($id2 == 1 ? '' : ' '.$id2), ItemDB::$itemNameToId))
				$id2++;
			ItemDB::$itemNameToId[$name2.($id2 == 1 ? '' : ' '.$id2)] = intval($split[0]);
		}
	}

	public static function findID($nameLike, $similarityArg = 0.65) {
		$nameLike = preg_replace("/[^a-z0-9 ]+/", '', strtolower($nameLike));
		$similar = array();

		$fMapSimilarity = function($name) use ($nameLike, $similarityArg) {
			if ($name == '')
				return null;
			$iname = strtolower($name);

			$similarity = similarity($iname, $nameLike);
			if ($similarity < $similarityArg)
				return null;
			return $similarity;
		};

		foreach (ItemDB::$itemIdToName as $id => $name) {
			$similarity = $fMapSimilarity($name);
			if ($similarity != null)
				array_push($similar, array($id, $similarity));
		}
		usort($similar, function($o1, $o2){
			return $o1[1] == $o2[1] ? 0 : ($o1[1] > $o2[1] ? -1 : 1);
		});
		return array_map(function($o) {
			return $o[0];
		}, $similar);
	}

	public static function get($id) {
		if (file_exists(Core::$basePath.'/data/Item/'.$id.'.json')) {
			$item = Item::fromJSON(json_decode(file_get_contents(Core::$basePath.'/data/Item/'.$id.'.json'), true));
			if ($item != null)
				ItemDB::$items[$id] = $item;
			return $item;
		}
		return null;
	}

	public static function fetch($arg) {
		$id = $arg;
		if (is_string($id))
			$id = ItemDB::$itemNameToId[preg_replace("/[^a-z0-9 ]+/", '', strtolower($id))];
		if (!array_key_exists($id, ItemDB::$items))
			return ItemDB::get($id);
		return ItemDB::$items[$id];
	}
}

?>
