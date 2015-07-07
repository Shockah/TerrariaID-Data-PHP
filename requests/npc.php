<?php

require_once('../Core.php');

$ids = null;
if (is_numeric($_GET['nameLike']))
	$ids = array($_GET['nameLike']);
else
	$ids = NPCDB::findID($_GET['nameLike']);

$text = '';
foreach ($ids as $id) {
	$npc = NPCDB::get($id);
	if ($npc == null)
		continue;

	if ($text !== '')
		$text .= ' ';
	$text .= $npc->textIRC(true);
	break;
	//$text .= count($results) === 1 ? $card->fulltextIRC() : $card->labelIRC();
}
echo($text);

if ($text === '')
	echo('No NPCs found.');

?>
