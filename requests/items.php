<?php

require_once('../Core.php');
$matches = null;

$input = trim($_GET['nameLike']);

$similarity = null;
$images = false;
$limit = 0;
$newlines = false;
$prop = null;
$rand = false;
$sort = array();
$filters = array();

$sortableFields = array(
	'id' => 'id',
	'value' => 'value',
	'rare' => 'rare',
	'damage' => 'damage',
	'pickaxe' => 'pickaxePower',
	'axe' => 'axePower',
	'hammer' => 'hammerPower',
	'fishing' => 'fishingPower',
	'bait' => 'baitPower'
);
$filterableFields = array(
	'quest' => 'questItem',
	'pickaxe' => 'pickaxePower',
	'axe' => 'axePower',
	'hammer' => 'hammerPower',
	'fishing' => 'fishingPower',
	'bait' => 'baitPower',
	'weapon' => null,
	'melee' => 'melee',
	'ranged' => 'ranged',
	'thrown' => 'thrown',
	'magic' => 'magic',
	'summon' => 'summon',
	'ammo' => 'ammo',
	'accessory' => 'accessory',
	'armor' => null,
	'head' => 'armorHead',
	'body' => 'armorBody',
	'legs' => 'armorLegs',
	'expert' => 'expert'
);
$filterExtras = array(
	'armor' => function($item){ return $item->armorHead != 0 || $item->armorBody != 0 || $item->armorLegs != 0; },
	'weapon' => function($item){ return $item->damage > 0 && ($item->useStyle > 1 || ($item->useStyle == 1 && !$item->consumable)); }
);

while (true) {
	if (preg_match('/^\-similarity ([01]\.\d+)/i', $input, $matches)) {
		$similarity = doubleval($matches[1]);
		$input = trim(substr($input, strlen($matches[0])));
	} else if (preg_match('/^\-limit (\d+)/i', $input, $matches)) {
		$limit = intval($matches[1]);
		$input = trim(substr($input, strlen($matches[0])));
	} else if (preg_match('/^\-prop (\w+)/i', $input, $matches)) {
		$prop = $matches[1];
		$input = trim(substr($input, strlen($matches[0])));
	} else if (preg_match('/^\-images/i', $input, $matches)) {
		$images = true;
		$newlines = true;
		$input = trim(substr($input, strlen($matches[0])));
	} else if (preg_match('/^\-rand/i', $input, $matches)) {
		$rand = true;
		$input = trim(substr($input, strlen($matches[0])));
	} else if (preg_match('/^\-newlines/i', $input, $matches)) {
		$newlines = true;
		$input = trim(substr($input, strlen($matches[0])));
	} else if (preg_match('/^\-sort(?:by)? (\w+) ((?:asc)|(?:desc))/i', $input, $matches)) {
		$sortBy = $matches[1];
		$sortOrder = 1;
		if (isset($matches[2])) {
			$sortOrder = $matches[2];
			$sortOrder = strtolower($sortOrder) == 'desc' ? -1 : 1;
		}
		if (array_key_exists($sortBy, $sortableFields))
			array_push($sort, array($sortBy, $sortOrder));
		$input = trim(substr($input, strlen($matches[0])));
	} else if (preg_match('/^\-filter ([\+\-])(\w+)(?:,([\+\-])(\w+))*/i', $input, $matches)) {
		for ($i = 0; $i < count($matches) - 1; $i += 2) {
			$filterBool = $matches[$i + 1] == '+';
			$filterField = strtolower($matches[$i + 2]);
			if (array_key_exists($filterField, $filterableFields))
				array_push($filters, array($filterField, $filterBool));
		}
		$input = trim(substr($input, strlen($matches[0])));
	} else
		break;
}

$ids = null;
if (is_numeric($input))
	$ids = array($input);
else {
	if (preg_match('/^(\d+)\-(\d+)$/', $input, $matches)) {
		$ids = array();
		for ($i = $matches[1]; $i <= $matches[2]; $i++)
			array_push($ids, $i);
	} else if (preg_match('/^[\-\d\s]+$/', $input)) {
		$ids = preg_split('/\s+/', $input);
	} else {
		if (trim($input) == '') {
			$ids = array_keys(ItemDB::$itemIdToName);
		} else {
			$split = preg_split('/\s*[;,]\s*/', $input);
			$ids = array();

			if ($similarity === null)
				$similarity = count($split) > 1 ? 0.9 : 0.65;
			foreach ($split as $one)
				$ids = array_merge($ids, ItemDB::findID($one, $similarity));
		}
	}
}

$items = array();
foreach ($ids as $id) {
	$item = ItemDB::get($id);
	if ($item != null)
		array_push($items, $item);
}

if (!empty($filters)) {
	$items = array_filter($items, function($i) use ($filters, $filterableFields, $filterExtras){
		foreach ($filters as $filter) {
			$field = $filterableFields[$filter[0]];
			$state = $filter[1];
			if ($field != null)
				if (($i->{$field} <= 0) ^ !$state)
					return false;
			if (array_key_exists($filter[0], $filterExtras))
				if ((!$filterExtras[$filter[0]]($i)) ^ !$state)
					return false;
		}
		return true;
	});
}

if (!empty($sort)) {
	$items = array_filter($items, function($i) use ($sort, $sortableFields){
		foreach ($sort as $sortOne) {
			if ($sortOne[0] == 'id')
				return $i->{$sortableFields[$sortOne[0]]} != 0;
			return $i->{$sortableFields[$sortOne[0]]} > 0;
		}
	});
	usort($items, function($i1, $i2) use ($sort, $sortableFields){
		foreach ($sort as $sortOne) {
			if ($i1->{$sortableFields[$sortOne[0]]} != $i2->{$sortableFields[$sortOne[0]]})
				return ($i1->{$sortableFields[$sortOne[0]]} < $i2->{$sortableFields[$sortOne[0]]} ? -1 : 1) * $sortOne[1];
		}
		return 0;
	});
}
if ($rand)
	shuffle($items);

$text = '';
foreach ($items as $item) {
	if ($prop == null) {
		if ($images) {
			$url = $item->imageURL();
			if ($url != null) {
				if ($text !== '')
					$text .= $newlines ? "\n" : ' ';
				$text .= $item->labelIRC(true);
				$text .= ' '.$url;
			}
		} else {
			if ($text !== '')
				$text .= $newlines ? "\n" : ' ';
			$text .= $item->labelIRC(true);
		}
	} else {
		$val = $item->getProp($prop);
		if ($val != null) {
			if ($text !== '')
				$text .= $newlines ? "\n" : ' ';
			$text .= $val;
		}
	}

	if (--$limit == 0)
		break;
}
echo(trim($text));

if ($text === '')
	echo('No items found.');

?>
