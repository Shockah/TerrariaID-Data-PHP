<?php

class Recipe {
	public static function fromJSON($item, $json) {
		$recipe = new Recipe();
		$recipe->item = $item;
		Recipe::deserialize($recipe, $json);
		return $recipe;
	}

	public static function deserialize($obj, $json) {
		$obj->json = $json;
		$obj->creates = value($json, 'creates', 1);
		$obj->createsWhat = ItemDB::fetch(value($json, 'createsWhat', $obj->item == null ? 0 : $obj->item->netID));
		$obj->anyWood = value($json, 'anyWood', false);
		$obj->anyIronBar = value($json, 'anyIronBar', false);
		$obj->anyPressurePlate = value($json, 'anyPressurePlate', false);
		$obj->anySand = value($json, 'anySand', false);
		$obj->anyFragment = value($json, 'anyFragment', false);
		$obj->alchemy = value($json, 'alchemy', false);

		$items = value($json, 'items', array());
		foreach ($items as $itemName => $itemCount) {
			$item = ItemDB::fetch($itemName);
			if ($item != null)
				array_push($obj->items, array($item, $itemCount));
		}

		$tiles = value($json, 'tiles', array());
		foreach ($tiles as $tileName)
			array_push($obj->tiles, $tileName);
	}

	public $json;
	public $item = null;
	public $createsWhat = null;
	public $creates = 1;
	public $items = array();
	public $tiles = array();
	public $anyWood, $anyIronBar, $anyPressurePlate, $anySand, $anyFragment, $alchemy;

	public function labelIRC() {
		return $this->createsWhat->labelIRC();
	}

	public function textIRC() {
		$txt = $this->creates."x ".$this->labelIRC().': ';

		for ($i = 0; $i < count($this->items); $i++) {
			if ($i != 0)
				$txt .= ', ';
			$recItem = $this->items[$i];

			$text = $recItem[0]->labelIRC();
			if ($this->anyWood && $recItem[0]->name == 'Wood')
				$text = Item::raritySpan(0, false, false, 'Any Wood');
			else if ($this->anyIronBar && $recItem[0]->name == 'Iron Bar')
				$text = Item::raritySpan(0, false, false, 'Iron/Lead Bar');
			else if ($this->anyPressurePlate && $recItem[0]->name == 'Gray Pressure Plate')
				$text = Item::raritySpan(0, false, false, 'Any Pressure Plate');
			else if ($this->anySand && $recItem[0]->name == 'Sand Block')
				$text = Item::raritySpan(0, false, false, 'Any Sand');
			else
				$text = $recItem[0]->labelIRC();

			$txt .= $recItem[1].'x '.$text;
		}

		$anyTxt = '';
		if ($this->anyFragment) {
			if ($anyTxt != '')
				$anyTxt .= ', ';
			$anyTxt .= 'fragment';
		}
		if ($anyTxt != '')
			$txt .= ' (any '.$anyTxt.')';

		if (!empty($this->tiles) || $this->alchemy) {
			$txt .= ' @ ';
			for ($i = 0; $i < count($this->tiles); $i++) {
				if ($i != 0)
					$txt .= ', ';
				$tile = $this->tiles[$i];
				if ($tile[0] == '@')
					$tile = substr($tile, 1);
				$txt .= $tile;
			}

			if ($this->alchemy) {
				if ($i != 0)
					$txt .= ', ';
				$txt .= 'Alchemy Station';
			}
		}
		return $txt;
	}
}

?>
