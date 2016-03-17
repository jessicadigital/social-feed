<?php

namespace JessicaDigital\SocialFeed\Items;

class Item {
	/** @var string */
	public $service;
	/** @var string */
	public $text;
	/** @var string */
	public $link;
	/** @var string */
	public $id;
	/** @var int */
	public $created;
	/** @var User */
	public $user;
	/** @var Media */
	public $media;

    public function image() {
        return $this->media->image != null ? $this->media->image : ($this->media->video == null ? null : $this->media->video->image);
    }
}
