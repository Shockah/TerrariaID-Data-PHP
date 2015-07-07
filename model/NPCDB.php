<?php

class NPCDB {
	public static $npcIdToName = array();
	public static $npcNameToId = array();

	public static function __init() {
		$file = file_get_contents(Core::$basePath.'/data/NPC-ids.txt');
		$lines = explode("\n", $file);
		foreach ($lines as $line) {
			$split = explode(':', $line);
			if ($split[0] == 0)
				continue;

			$name = preg_replace("/[^a-z0-9 ]+/", '', strtolower($split[1]));

			NPCDB::$npcIdToName[intval($split[0])] = $name;
			$name2 = $name;
			$id2 = 1;

			while (array_key_exists($name2.($id2 == 1 ? '' : ' '.$id2), NPCDB::$npcNameToId))
				$id2++;
			NPCDB::$npcNameToId[$name2.($id2 == 1 ? '' : ' '.$id2)] = array(intval($split[0]), $name2);
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

		foreach (NPCDB::$npcIdToName as $id => $name) {
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
		if (file_exists(Core::$basePath.'/data/NPC/'.$id.'.json'))
			return NPC::fromJSON(json_decode(file_get_contents(Core::$basePath.'/data/NPC/'.$id.'.json'), true));
		return null;
	}
}

?>
