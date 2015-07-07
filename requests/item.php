<?php

require_once('../Core.php');

$ids = null;
if (is_numeric($_GET['nameLike']))
	$ids = array($_GET['nameLike']);
else
	$ids = ItemDB::findID($_GET['nameLike']);

$text = '';
foreach ($ids as $id) {
	$item = ItemDB::get($id);
	if ($item == null)
		continue;

	if ($text !== '')
		$text .= ' ';
	$text .= $item->textIRC(true);
	break;
	//$text .= count($results) === 1 ? $card->fulltextIRC() : $card->labelIRC();
}
echo($text);

if ($text === '')
	echo('No items found.');

?>
