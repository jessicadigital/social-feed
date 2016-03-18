<?php

namespace JessicaDigital\SocialFeed\Media;

class Instagram extends Media {
    public $id;
    public $image;
    public $type = 'instagram';
    public $url;
    
    public function __construct($url = '') {
        $this->url = $url;
        $data = @file_get_contents('http://api.instagram.com/oembed?url='.urlencode($this->url));
        if (!empty($data)) {
            $info = json_decode($data);
            if (strpos($info->html, 'video') > -1) {
                $this->id = $matches[0];
                $this->image = empty($info->thumbnail_url) ? null : $info->thumbnail_url;
            }
            else {
                $this->image = empty($info->thumbnail_url) ? null : $info->thumbnail_url;
            }
        }
    }
}

