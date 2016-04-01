<?php

namespace JessicaDigital\SocialFeed\Items;

use JessicaDigital\SocialFeed\Items\User;

class PinterestItem extends Item {
    public $service = 'pinterest';
    
    public function __construct($data) {

        $user = new User();
        $this->id = $data->id;
        $this->created = false;
        if (!empty($data->pinner->id)) {
            $user->id = $data->pinner->id;
            $user->image = $data->pinner->image_small_url;
            $user->link = $data->pinner->profile_url;
            $user->location = $data->pinner->location;
            $user->name = $data->pinner->full_name;
            $user->profile = $data->pinner->about;
        }
        $this->dominant_color = $data->dominant_color;
        $this->likes = $data->like_count;
        $this->link = $data->link;
        $this->shares = $data->repin_count;
        $this->text = $data->description;
        
        if (!empty($data->images)) {
            // Fetch the first image
            $imagedata = reset($data->images);
            $media = $this->mediaFromUrl($imagedata->url);
        }

        $this->user = $user;
        if (!empty($media->image) || !empty($media->video)) {
            $this->media = $media;
        }
    }
}