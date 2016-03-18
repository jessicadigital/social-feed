<?php

namespace JessicaDigital\SocialFeed\Media;

class Vimeo extends Media {
    public $id;
    public $type = 'vimeo';
    
    public function __construct(string $url, int $id = 0) {
        $this->url = $url;
        $this->id = $id;
    }
}
