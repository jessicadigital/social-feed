<?php

namespace JessicaDigital\SocialFeed\Media;

class Youtube extends Media {
    public $id;
    public $image;
    public $type = 'youtube';
    
    public function __construct($url, $id = 0) {
        $this->url = $url;
        $this->id = $id;
        $this->image = 'http://img.youtube.com/vi/'.$this->id.'/hqdefault.jpg';
    }
}
