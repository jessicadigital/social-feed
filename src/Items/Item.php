<?php

namespace JessicaDigital\SocialFeed\Items;


use JessicaDigital\SocialFeed\Media\Media;

abstract class Item {
    public $created;
    public $id;
    public $link;
    public $livetext;
    public $media;
    public $service;
    public $text;
    public $user;

    public function image() {
        return !empty($this->media->image) ? $this->media->image : (empty($this->media->video) ? null : $this->media->video->image);
    }
    
    protected function mediaFromUrl($url) {
        $media = null;
        if (preg_match('/vine\.co\/v\/([a-z0-9]+)/i', $url, $matches)) {
            $media = new Media\Vine($url, $matches[1]);
        }
        else if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches)) {
            $media = new Media\Youtube($url, $matches[1]);
        }
        else if (preg_match('/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/i', $url, $matches)) {
            $media = new Media\Vimeo($url, $matches[3]);
        }
        else if (preg_match('/instagram\.com\/p\/([a-z0-9-_]+)\//i', $url, $matches)) {
            $media = new Media\Instagram($url);
        }
        else if (preg_match('/facebook\.com\/.+\/videos\/([0-9]+)\//i', $url, $matches)) {
            $media = new Media\Facebook($url, $matches[1]);
        }
        return $media;
    }
}
