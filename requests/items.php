<?php

require_once('../Core.php');
$matches = null;

$input = $_GET['nameLike'];

$similarity = null;
$images = false;
$limit = 0;
$newlines = false;
$sort = array();

while (true) {
	if (preg_match('/^-similarity ([01]\.\d+) /i', $input, $matches)) {
		$similarity = doubleval($matches[1]);
		$input = substr($input, strlen($matches[0]));
	} else if (preg_match('/^-limit (\d+) /i', $input, $matches)) {
		$limit = intval($matches[1]);
		$input = substr($input, strlen($matches[0]));
	} else if (preg_match('/^-images /i', $input, $matches)) {
		$images = true;
		$newlines = true;
		$input = substr($input, strlen($matches[0]));
	} else if (preg_match('/^-newlines /i', $input, $matches)) {
		$newlines = true;
		$input = substr($input, strlen($matches[0]));
	} else if (preg_match('/^-sortby (\w+) ((?:asc)|(?:desc)) /i', $input, $matches)) {
		$sortBy = $matches[1];
		$sortOrder = 1;
		if (isset($matches[2])) {
			$sortOrder = $matches[2];
			$sortOrder = strtolower($sortOrder) == 'desc' ? -1 : 1;
		}
		if ($sortBy == 'id' || $sortBy == 'value' || $sortBy == 'rare' || $sortBy == 'damage')
			array_push($sort, array($sortBy, $sortOrder));
		$input = substr($input, strlen($matches[0]));
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
		$split = preg_split('/\s*[;,]\s*/', $input);
		$ids = array();

		if ($similarity === null)
			$similarity = count($split) > 1 ? 0.9 : 0.65;
		foreach ($split as $one)
			$ids = array_merge($ids, ItemDB::findID($one, $similarity));
	}
}

$items = array();
foreach ($ids as $id) {
	$item = ItemDB::get($id);
	if ($item != null)
		array_push($items, $item);
}

if (!empty($sort)) {
	usort($items, function($i1, $i2) use ($sort){
		foreach ($sort as $sortOne) {
			if ($i1->{$sortOne[0]} != $i2->{$sortOne[0]})
				return ($i1->{$sortOne[0]} < $i2->{$sortOne[0]} ? -1 : 1) * $sortOne[1];
		}
		return 0;
	});
}

$text = '';
foreach ($items as $item) {
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

	if (--$limit == 0)
		break;
}
echo($text);

if ($text === '')
	echo('No items found.');

?>
