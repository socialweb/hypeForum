<?php

class hjForumTopic extends hjForum {

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = "hjforumtopic";
	}

}