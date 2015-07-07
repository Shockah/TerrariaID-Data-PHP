<?php

require_once('../Core.php');

$breakdown = false;
if (isset($_GET['breakdown']))
	$breakdown = $_GET['breakdown'];

$ids = null;
if (is_numeric($_GET['nameLike']))
	$ids = array($_GET['nameLike']);
else
	$ids = ItemDB::findID($_GET['nameLike']);

$text = '';
$item = null;
foreach ($ids as $id) {
	$item = ItemDB::get($id);
	if ($item == null)
		continue;

	$recipes = $item->parseRecipes();
	foreach ($recipes as $recipe) {
		$recipe2 = $recipe;
		//if ($breakdown)
		//	$recipe2 = $recipe2->breakdown();
		if ($text != '')
			$text .= "\n";
		$text .= $recipe2->textIRC();
	}
	break;
	//$text .= count($results) === 1 ? $card->fulltextIRC() : $card->labelIRC();
}
echo($text);

if ($text === '') {
	if ($item == null)
		echo('No items found.');
	else
		echo('No recipes found for '.$item->labelIRC().'.');
}

?>
