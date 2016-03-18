<?php

namespace JessicaDigital\SocialFeed\Media;

class Facebook extends Media {
    public $id;
    public $image;
    public $type = 'facebook';
    
    public function __construct(string $url, int $id = 0) {
        $this->url = $url;
        $this->id = $id;
        $this->image = 'https://graph.facebook.com/'.$this->id.'/picture?type=large';
    }
}
