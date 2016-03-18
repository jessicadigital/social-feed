<?php

namespace JessicaDigital\SocialFeed\Media;

class Facebook extends Media {
    public $id;
    public $image;
    public $type = 'facebook';
    
    public function __construct($url, $id = 0) {
        $this->url = $url;
        $this->id = $id;
        $this->image = empty($id) ? $url : 'https://graph.facebook.com/'.$this->id.'/picture?type=large';
    }
}
