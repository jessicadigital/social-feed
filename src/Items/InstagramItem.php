<?php

namespace JessicaDigital\SocialFeed\Items;

use JessicaDigital\SocialFeed\Media\Instagram;

class InstagramItem extends Item {
    public $service = 'instagram';

    public function __construct($data) {

        $user = new \JessicaDigital\SocialFeed\Items\User();
        $user->handle = $data->user->username;
        $user->id = $data->user->id;
        $user->image = $data->user->profile_picture;
        $user->link = 'https://instagram.com/'.$data->user->username;
        $user->name = $data->user->full_name;
        $this->user = $user;

        $this->id = $data->id;
        $this->created = (int) $data->created_time;
        $this->link = $data->link;
        $this->text = $data->caption->text;
        $this->livetext = $this->linkify($data->caption->text);

        if (!empty($data->images->standard_resolution->url)) {
            $this->media = new Instagram(str_replace('s150x150', 's640x640', $data->images->thumbnail->url));
        }
    }

    public function linkify($text) {
        // linkify URLs
        $text = preg_replace(
            '/(https?:\/\/\S+)/',
            '<a href="\1" target="_blank">\1</a>',
            $text
        );

        // linkify instagram users
        $text = preg_replace(
            '/(^|\s)@(\w+)/',
            '\1@<a href="https://instagram.com/\2" target="_blank">\2</a>',
            $text
        );

        // linkify tags
        $text = preg_replace(
            '/(^|\s)#(\w+)/',
            '\1#<a href="https://www.instagram.com/explore/tags/\2/" target="_blank">\2</a>',
            $text
        );

        return $text;
    }
}
