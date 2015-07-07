<?php

require_once('../Core.php');

$breakdown = false;
if (isset($_GET['breakdown']))
	$breakdown = $_GET['breakdown'];

$listOnly = false;

$name = $_GET['nameLike'];
$ids = null;
if (is_numeric($name))
	$ids = array($name);
else {
	if (str_startswith(strtolower($name), '-listonly ')) {
		$listOnly = true;
		$name = substr($name, strlen('-listonly '));
	}
	$ids = ItemDB::findID($name);
}

$text = '';
$item = null;
foreach ($ids as $id) {
	$item = ItemDB::get($id);
	if ($item == null)
		continue;

	$recipes = $item->parseReverseRecipes();
	foreach ($recipes as $recipe) {
		$recipe2 = $recipe;
		//if ($breakdown)
		//	$recipe2 = $recipe2->breakdown();
		if ($text != '')
			$text .= "\n";
		$text .= $listOnly ? $recipe2->labelIRC() : $recipe2->textIRC();
	}
	break;
	//$text .= count($results) === 1 ? $card->fulltextIRC() : $card->labelIRC();
}
echo($text);

if ($text === '') {
	if ($item == null)
		echo('No items found.');
	else
		echo('No recipes found using '.$item->labelIRC().'.');
}

?>
