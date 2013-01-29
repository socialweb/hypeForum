<?php

class hjForum extends hjObject {

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = "hjforum";
	}

	public function countTopics($recursive = false) {
		return $this->getLatestTopics(0, true, $recursive);
	}

	public function countPosts($recursive = false) {
		return $this->getLatestPosts(0, true, $recursive);
	}

	public function getLatestTopics($limit = 10, $count = false, $recursive = false) {
		return hj_forum_get_latest_topics($this->guid, $limit, $count, $recursive);
	}

	public function getLatestTopic($recursive = false) {
		$topics = $this->getLatestTopics(1, false, $recursive);
		return ($topics) ? $topics[0] : false;
	}

	public function getLatestPosts($limit = 10, $count = false, $recursive = false) {
		return hj_forum_get_latest_posts($this->guid, $limit, $count, $recursive);
	}

	public function getLatestPost($recursive = false) {
		$posts = $this->getLatestPosts(1, false, $recursive);
		return ($posts) ? $posts[0] : false;
	}

}

