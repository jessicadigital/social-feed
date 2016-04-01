<?php

namespace JessicaDigital\SocialFeed\Media;

class Generic extends Media {
    public $image;
    public $type = 'generic';
    
    public function __construct($url) {
        $this->url = $url;
        $this->image = $url;
    }
}
