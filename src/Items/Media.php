<?php

namespace JessicaDigital\SocialFeed\Items;

class Media {
	/** @var string */
	public $image;
	/** @var Video */
	public $video;
    /** @var Array */
    public $hash;

    public function calcHash() {
        $phasher = \PHasher::Instance();
        $url = $this->image != null ? $this->image : ($this->video == null ? null : $this->video->image);
        if ($url == null) return;
        $resource = imagecreatefromstring(file_get_contents($url));
        try {
            $hash = $phasher->FastHashImage($resource);
            $this->hash = $phasher->HashAsString($hash);
        } catch (\Exception $e) {
            $this->hash = null;
        }
    }
}