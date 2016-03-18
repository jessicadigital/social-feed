<?php

namespace JessicaDigital\SocialFeed\Media;

class Vine extends Media {
    public $id;
    public $image;
    public $type = 'vine';
    
    public function __construct(string $url, int $id = 0) {
        $this->url = $url;
        $this->id = $id;
        $vine = @file_get_contents('http://vine.co/v/'.$this->id);
        if (!empty($vine)) {
            preg_match('/property="og:image" content="(.*?)"/', $vine, $images);
            if (isset($images[1]) && $images[1] != '') {
                $this->image = $images[1];
            }
        }
    }
}
