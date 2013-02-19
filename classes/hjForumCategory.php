<?php

class hjForumCategory extends hjCategory {

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = "hjforumcategory";
	}

	public function save() {
		if ($guid = parent::save()) {
			if (!isset($this->priority))
				$this->priority = 0;
		}
		return $guid;
	}
	
	public function getURL() {
		return $this->getContainerEntity()->getURL();
	}
}

?>
