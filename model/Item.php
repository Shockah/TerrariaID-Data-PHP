<?php

class Item extends ModelObject {
	const IMAGE_URLS = true;

	public static function fromJSON($json) {
		$item = new Item();
		Item::deserialize($item, $json);
		return $item;
	}

	public static function deserialize($obj, $json) {
		$obj->json = $json;
		$obj->id = value($json, 'type');
		$obj->netID = value($json, 'netID', $obj->id);
		$obj->name = value($json, 'name');
		$obj->size = value($json, 'size');
		$obj->displayName = value($json, 'displayName', $obj->name);
		$obj->tooltip = value($json, 'tooltip', array());
		$obj->rare = value($json, 'rare', 0);
		$obj->questItem = value($json, 'questItem', false);
		$obj->expert = value($json, 'expert', false);
		$obj->maxStack = value($json, 'maxStack', 1);
		$obj->scale = value($json, 'scale');
		$obj->consumable = value($json, 'consumable');
		$obj->useStyle = value($json, 'useStyle', 0);
		$obj->useAnimation = value($json, 'useAnimation', 0);
		$obj->useTime = value($json, 'useTime', 0);
		$obj->mana = value($json, 'mana', 0);
		$obj->range = value($json, 'tileBoost', 0);
		$obj->pickaxePower = value($json, 'pick', 0);
		$obj->axePower = value($json, 'axe', 0) * 5;
		$obj->hammerPower = value($json, 'hammer', 0);
		$obj->fishingPower = value($json, 'fishingPole', 0);
		$obj->baitPower = value($json, 'bait', 0);
		$obj->damage = value($json, 'damage', 0);
		$obj->knockback = value($json, 'knockback', 0.0);
		$obj->autoReuse = value($json, 'autoReuse', false);
		$obj->useTurn = value($json, 'useTurn', false);
		$obj->critChance = value($json, 'crit', 0);
		$obj->melee = value($json, 'melee', false);
		$obj->ranged = value($json, 'ranged', false);
		$obj->thrown = value($json, 'thrown', false);
		$obj->magic = value($json, 'magic', false);
		$obj->summon = value($json, 'summon', false);
		$obj->ammo = value($json, 'ammo', 0);
		$obj->accessory = value($json, 'accessory', false);
		$obj->armorHead = value($json, 'armorHead', false);
		$obj->armorBody = value($json, 'armorBody', false);
		$obj->armorLegs = value($json, 'armorLegs', false);
		$obj->vanity = value($json, 'vanity', false);
		$obj->defense = value($json, 'defense', 0);
		$obj->healLife = value($json, 'healLife', 0);
		$obj->value = value($json, 'value', 0);
		$obj->useSound = value($json, 'useSound', 0);
		$obj->createTile = value($json, 'createTile', -1);
		$obj->createWall = value($json, 'createWall', -1);

		if ($obj->questItem)
			$obj->rare = 100;
	}

	public static function raritySpan($rare, $questItem, $expert, $text) {
		if ($expert)
			return IRC::BOLD.'[['.ModelObject::toRainbow($text).IRC::RESET.IRC::BOLD.']]'.IRC::RESET;
		if ($questItem)
			return IRC::BOLD.IRC::COLOR_ORANGE.'[['.$text.']]'.IRC::RESET;
		switch ($rare) {
			case 1: return IRC::BOLD.IRC::COLOR_BLUE.'['.$text.']'.IRC::RESET;
			case 2: return IRC::BOLD.IRC::COLOR_DGREEN.'['.$text.']'.IRC::RESET;
			case 3: return IRC::BOLD.IRC::COLOR_ORANGE.'['.$text.']'.IRC::RESET;
			case 4: return IRC::BOLD.IRC::COLOR_RED.'['.$text.']'.IRC::RESET;
			case 5: return IRC::BOLD.IRC::COLOR_DVIOLET.'['.$text.']'.IRC::RESET;
			case 6: return IRC::BOLD.IRC::COLOR_VIOLET.'['.$text.']'.IRC::RESET;
			case 7: return IRC::BOLD.IRC::COLOR_LGREEN.'['.$text.']'.IRC::RESET;
			case 8: return IRC::BOLD.IRC::COLOR_YELLOW.'['.$text.']'.IRC::RESET;
			case 9: return IRC::BOLD.IRC::COLOR_LCYAN.'['.$text.']'.IRC::RESET;
			case 10: return IRC::BOLD.IRC::COLOR_DRED.'['.$text.']'.IRC::RESET;
			case 11: return IRC::BOLD.IRC::COLOR_VIOLET.'[['.$text.']]'.IRC::RESET;
		}
		if ($rare < 0)
			return IRC::BOLD.IRC::COLOR_DGRAY.'['.$text.']'.IRC::RESET;
		if ($rare > 11)
			return IRC::BOLD.'[['.ModelObject::toRainbow($text).IRC::RESET.IRC::BOLD.']]'.IRC::RESET;
		return IRC::BOLD.'['.$text.']'.IRC::RESET;
	}

	public static function knockbackValueText($knockback) {
		if ($knockback <= 0)
			return "No";
		if ($knockback <= 1.5)
			return "Extremely weak";
		if ($knockback <= 3)
			return "Very weak";
		if ($knockback <= 4)
			return "Weak";
		if ($knockback <= 6)
			return "Average";
		if ($knockback <= 7)
			return "Strong";
		if ($knockback <= 9)
			return "Very strong";
		if ($knockback <= 11)
			return "Extremely Strong";
		return "Insane";
	}

	public static function speedValueText($speed) {
		if ($speed <= 8)
			return "Insanely fast";
		if ($speed <= 20)
			return "Very fast";
		if ($speed <= 25)
			return "Fast";
		if ($speed <= 30)
			return "Average";
		if ($speed <= 35)
			return "Slow";
		if ($speed <= 45)
			return "Very slow";
		if ($speed <= 55)
			return "Extremely slow";
		return "Snail";
	}

	public $json;
	public $id, $netID;
	public $name, $displayName, $tooltip, $rare, $questItem, $expert, $maxStack;
	public $size, $scale;
	public $consumable, $useStyle;
	public $useAnimation, $useTime, $mana, $range;
	public $pickaxePower, $axePower, $hammerPower, $fishingPower, $baitPower;
	public $damage, $knockback, $autoReuse, $useTurn;
	public $melee, $ranged, $thrown, $magic, $summon, $ammo;
	public $accessory, $armorHead, $armorBody, $armorLegs, $defense, $healLife;
	public $value;
	public $useSound, $createTile, $createWall;

	public function labelIRC($withID = false) {
		if (!$withID)
			return $this->myRaritySpan($this->displayName);
		$idText = '';
		if ($this->netID != $this->id)
			$idText .= $this->netID.'/';
		$idText .= $this->id;
		return $this->myRaritySpan($this->displayName.'|'.$idText);
	}

	public function textIRC($withID = false) {
		$txt = $this->labelIRC($withID);
		if ($this->maxStack > 1)
			$txt .= ' x'.$this->maxStack;

		if ($this->damage > 0) {
			$txt .= "\n".$this->damage.' ';
			$damageType = '';
			if ($this->melee) {
				if ($damageType != '')
					$damageType .= '/';
				$damageType .= 'melee';
			}
			if ($this->ranged) {
				if ($damageType != '')
					$damageType .= '/';
				$damageType .= 'ranged';
			}
			if ($this->thrown) {
				if ($damageType != '')
					$damageType .= '/';
				$damageType .= 'thrown';
			}
			if ($this->magic) {
				if ($damageType != '')
					$damageType .= '/';
				$damageType .= 'magic';
			}
			if ($this->summon) {
				if ($damageType != '')
					$damageType .= '/';
				$damageType .= 'summon';
			}
			if ($damageType != '')
				$txt .= $damageType.' ';
			$txt .= 'damage';
			if ($this->ammo != 0)
				$txt .= ' ammo';

			if ($this->critChance > 0)
				$txt .= ', '.$this->critChance.'% critical chance';

			if ($this->knockback > 0)
				$txt .= ', '.strtolower($this->myKnockbackValueText()).' ('.(round($this->knockback * 10) / 10).') knockback';

			if ($this->ammo == 0 && !$this->consumable) {
				$useTimeMin = min($this->useTime, $this->useAnimation);
				$useTimeMax = max($this->useTime, $this->useAnimation);
				$txt .= ', '.strtolower($this->mySpeedValueText()).' ('.$useTimeMin.($useTimeMin != $useTimeMax ? '/'.$useTimeMax : '').') speed';

				if ($this->autoReuse)
					$txt .= ', auto-swing';
				if ($this->useTurn)
					$txt .= ', swing-turning';
			}

			if ($this->mana > 0)
				$txt .= ', '.$this->mana.' mana';
		}

		$toolPower = '';
		if ($this->pickaxePower > 0) {
			if ($toolPower != '')
				$toolPower .= ', ';
			$toolPower .= $this->pickaxePower.'% pickaxe power';
		}
		if ($this->axePower > 0) {
			if ($toolPower != '')
				$toolPower .= ', ';
			$toolPower .= $this->axePower.'% axe power';
		}
		if ($this->hammerPower > 0) {
			if ($toolPower != '')
				$toolPower .= ', ';
			$toolPower .= $this->hammerPower.'% hammer power';
		}
		if ($this->fishingPower > 0) {
			if ($toolPower != '')
				$toolPower .= ', ';
			$toolPower .= $this->fishingPower.'% fishing power';
		}
		if ($this->baitPower > 0) {
			if ($toolPower != '')
				$toolPower .= ', ';
			$toolPower .= $this->baitPower.'% bait power';
		}
		if ($toolPower != '') {
			$txt .= "\n".$toolPower;

			if ($this->range != 0)
				$txt .= ', '.($this->range > 0 ? '+' : '-').abs($this->range).' tile range';
		}

		$equipTxt = '';
		if ($this->armorHead) {
			if ($equipTxt != '')
				$equipTxt .= ', ';
			$equipTxt .= 'head armor';
		}
		if ($this->armorBody) {
			if ($equipTxt != '')
				$equipTxt .= ', ';
			$equipTxt .= 'body armor';
		}
		if ($this->armorLegs) {
			if ($equipTxt != '')
				$equipTxt .= ', ';
			$equipTxt .= 'legs armor';
		}
		if ($this->accessory) {
			if ($equipTxt != '')
				$equipTxt .= ', ';
			$equipTxt .= 'accessory';
		}

		if ($this->vanity || $equipTxt != '') {
			$txt .= "\n";
			if ($this->vanity)
				$txt .= 'vanity';
			if ($equipTxt != '') {
				if ($this->vanity)
					$txt .= ' ';
				$txt .= $equipTxt;
			}
		}

		if ($this->createTile != -1)
			$txt .= "\nplaceable block";
		if ($this->createWall != -1)
			$txt .= "\nplaceable wall";

		if ($this->defense != 0)
			$txt .= "\n".$this->defense." defense";

		if ($this->healLife != 0)
			$txt .= "\nHeals for ".$this->healLife;

		/*if ($this->expert)
			$txt .= "\n".ModelObject::toRainbow('Expert').IRC::RESET;*/

		if (!empty($this->tooltip)) {
			$ttipText = '';
			foreach ($this->tooltip as $line) {
				if ($ttipText != '')
					$ttipText .= "\n";
				$ttipText .= IRC::ITALIC.$line.IRC::RESET;
			}
			$txt .= "\n".$ttipText;
		}

		$craftTxt = '';
		$recipes = $this->parseRecipes();
		if (!empty($recipes)) {
			if ($craftTxt != '')
				$craftTxt .= ', ';
			$craftTxt .= "craftable";
		}
		$reverseRecipes = $this->parseReverseRecipes();
		if (!empty($reverseRecipes)) {
			if ($craftTxt != '')
				$craftTxt .= ', ';
			$craftTxt .= "material";
		}

		if ($craftTxt != '')
			$txt .= "\n".$craftTxt;

		if ($this->value > 0 && ($this->id < 71 || $this->id > 74))
			$txt .= "\n".$this->myCoinValueText();

		if (Item::IMAGE_URLS) {
			$imageURL = $this->imageURL();
			if ($imageURL != null)
				$txt .= "\n".$imageURL;
		}

		return $txt;
	}

	public function imageURL() {
		if (file_exists(Core::$basePath.'/images/Item_'.$this->id.'.png'))
			return Core::$baseURL.'/Item_'.$this->id.'.png';
		return null;
	}

	public function myRaritySpan($text) {
		return Item::raritySpan($this->rare, $this->questItem, $this->expert, $text);
	}

	public function myKnockbackValueText() {
		return Item::knockbackValueText($this->knockback);
	}

	public function mySpeedValueText() {
		return Item::speedValueText(min($this->useTime, $this->useAnimation));
	}

	public function myCoinValueText() {
		return ModelObject::coinValueText($this->value);
	}

	public function parseRecipes() {
		$ret = array();
		if (array_key_exists('recipes', $this->json)) {
			foreach ($this->json['recipes'] as $recipeJson) {
				$recipe = Recipe::fromJSON($this, $recipeJson);
				if ($recipe != null)
					array_push($ret, $recipe);
			}
		}
		return $ret;
	}

	public function parseReverseRecipes() {
		$ret = array();
		if (file_exists(Core::$basePath.'/datagen/reverse_recipes/'.$this->netID.'.json')) {
			$json = json_decode(file_get_contents(Core::$basePath.'/datagen/reverse_recipes/'.$this->netID.'.json'), true);
			foreach ($json as $recipeJson) {
				$recipe = Recipe::fromJSON(null, $recipeJson);
				if ($recipe != null)
					array_push($ret, $recipe);
			}
		}
		return $ret;
	}

	public function getProp($prop) {
		if ($prop == 'pretty_displayName')
			return $this->labelIRC(false);
		if ($prop == 'pretty_value')
			return $this->myCoinValueText();
		if ($prop == 'pretty_knockback')
			return $this->myKnockbackValueText();
		if ($prop == 'pretty_speed')
			return $this->mySpeedValueText();

		return parent::getProp($prop);
	}
}

?>
