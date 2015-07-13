<?php

class NPC extends ModelObject {
	const IMAGE_URLS = true;

	public static function fromJSON($json) {
		$npc = new NPC();
		NPC::deserialize($npc, $json);
		return $npc;
	}

	public static function deserialize($obj, $json) {
		$obj->json = $json;
		$obj->id = value($json, 'type');
		$obj->netID = value($json, 'netID', $obj->id);
		$obj->name = value($json, 'name');
		$obj->size = value($json, 'size');
		$obj->displayName = value($json, 'displayName', $obj->name);
		$obj->life = value($json, 'lifeMax');
		$obj->damage = value($json, 'damage', 0);
		$obj->defense = value($json, 'defense', 0);
		$obj->knockbackResist = value($json, 'knockbackResist', 0.0);
		$obj->friendly = value($json, 'friendly', false);
		$obj->boss = value($json, 'boss', false);
		$obj->townNPC = value($json, 'townNPC', false);
	}

	public $json;
	public $id, $netID;
	public $name, $displayName;
	public $size, $scale;
	public $value, $life, $damage, $defense, $knockbackResist, $friendly, $boss, $townNPC;

	public function labelIRC($withID = false) {
		if (!$withID)
			return IRC::BOLD.'['.$this->displayName.']'.IRC::RESET;
		$idText = '';
		if ($this->netID != $this->id)
			$idText .= $this->netID.'/';
		$idText .= $this->id;
		return IRC::BOLD.'['.$this->displayName.'|'.$idText.']'.IRC::RESET;
	}

	public function textIRC($withID = false) {
		$txt = $this->labelIRC($withID);

		$txt .= "\n".$this->life.' life';
		$txt .= ', '.$this->defense.' defense';
		if ($this->knockbackResist > 0)
			$txt .= ', '.(round($this->knockbackResist * 10) / 10).' knockback resist';
		if (!$this->friendly)
			$txt .= ', '.$this->damage.' damage';

		$txt .= "\n".($this->friendly ? 'friendly' : 'hostile');
		if ($this->townNPC)
			$txt .= ', town NPC';
		if ($this->boss)
			$txt .= ', boss';

		if ($this->value > 0)
			$txt .= "\n".$this->myCoinValueText();

		if (NPC::IMAGE_URLS) {
			$imageURL = $this->imageURL();
			if ($imageURL != null)
				$txt .= "\n".$imageURL;
		}

		return $txt;
	}

	public function imageURL() {
		if (file_exists(Core::$basePath.'/images/NPC_'.$this->id.'.png'))
			return Core::$baseURL.'/NPC_'.$this->id.'.png';
		return null;
	}

	public function myCoinValueText() {
		return ModelObject::coinValueText($this->value);
	}

	public function getProp($prop) {
		if ($prop == 'pretty_value')
			return $this->myCoinValueText();

		return parent::getProp($prop);
	}
}

?>
