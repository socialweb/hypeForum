<?php

class hjForumPost extends hjForumTopic {

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = "hjforumpost";
	}

	public function getURL() {
		return elgg_http_add_url_query_elements($this->getContainerEntity()->getURL(), array('__goto' => $this->guid)) . "#elgg-entity-$this->guid";
	}
}
