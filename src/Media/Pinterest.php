<?php

namespace JessicaDigital\SocialFeed\Media;

class Pinterest extends Media {
    public $image;
    public $type = 'pinterest';
    
    public function __construct($url) {
        $this->url = $url;
        $this->image = $url;
    }
}
