<?php

class hjForumCategory extends hjCategory {

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = "hjforumcategory";
	}

}

?>
