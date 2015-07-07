<?php

require_once('../Core.php');

$cachedIdToItem = array();
$cachedItemRecipes = array();

foreach (ItemDB::$itemIdToName as $itemId => $itemName) {
	$item = ItemDB::fetch($itemId);
	if ($item == null)
		continue;
	$recipes = $item->parseRecipes();
	foreach ($recipes as $recipe) {
		foreach ($recipe->items as $material) {
			if (!array_key_exists($material[0]->netID, $cachedIdToItem)) {
				$cachedIdToItem[$material[0]->netID] = $material[0];
				$cachedItemRecipes[$material[0]->netID] = array();
			}
			$recipe->json['createsWhat'] = $item->netID;
			array_push($cachedItemRecipes[$material[0]->netID], $recipe->json);
		}
	}
}

if (!empty($cachedIdToItem)) {
	if (!file_exists(Core::$basePath.'/datagen'))
		mkdir(Core::$basePath.'/datagen');
	if (!file_exists(Core::$basePath.'/datagen/reverse_recipes'))
		mkdir(Core::$basePath.'/datagen/reverse_recipes');

	foreach ($cachedIdToItem as $itemId => $item)
		file_put_contents(Core::$basePath.'/datagen/reverse_recipes/'.$itemId.'.json', json_encode($cachedItemRecipes[$itemId]));
	echo('Done!');
}

?>
