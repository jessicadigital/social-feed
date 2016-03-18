<?php

namespace JessicaDigital\SocialFeed\Items;

use JessicaDigital\SocialFeed\Items\User;
use JessicaDigital\SocialFeed\Media\Youtube;

class YoutubeItem extends Item {
    public $service = 'youtube';
    
    public function __construct($data) {
        
        $this->id = $data->etag;
        $this->created = strtotime($data->snippet->publishedAt);
        //public $link;
        //public $livetext;
        //public $media;
        //public $text;
        //public $user;
        
        $media = new Youtube();
        $this->media = $media;
        
        $user = new User();
        $user->id = $data->id->channelId;
        $user->link = 'https://www.youtube.com/channel/'.$data->id->channelId;
        $user->name = $data->snippet->channelTitle;
        $this->user = $user;
        var_dump($this);
        var_dump($data);exit;
        $user = new User();
        $this->id = $data->id;
        $this->created = strtotime($data->created_at);
        if (!empty($data->retweeted_status->user->id)) {
            $user->id = $data->retweeted_status->user->id;
            $user->handle = $data->retweeted_status->user->screen_name;
            $user->image = $data->retweeted_status->user->profile_image_url_https;
            $user->link = $data->retweeted_status->user->url;
            $user->name = $data->retweeted_status->user->name;
        } else {
            $user->id = $data->user->id;
            $user->handle = $data->user->screen_name;
            $user->image = $data->user->profile_image_url_https;
            $user->link = $data->user->url;
            $user->name = $data->user->name;
        }
        $this->link = 'https://twitter.com/'.$user->handle.'/status/'.$this->id;
        $this->text = $data->text;
        $this->livetext = $this->linkify($data->text);

        if (!empty($data->extended_entities->media)) {
            $img = $data->extended_entities->media[0];
            $media->image = $img->media_url_https;
        }

        if (isset($data->entities->urls)) {
            foreach ($data->entities->urls as $url) {
                $parsed = $this->mediaFromUrl($url->expanded_url);
                if (!empty($parsed->image) || !empty($parsed->video->id)) {
                    $media = $parsed;
                }
                break;
            }
        }

        $this->user = $user;
        if (!empty($media->image) && !empty($media->video)) {
            $this->media = $media;
        }
    }
    
    public function linkify($text) {
        // linkify URLs
        $text = preg_replace(
            '/(https?:\/\/\S+)/',
            '<a href="\1" target="_blank">\1</a>',
            $text
        );

        // linkify twitter users
        $text = preg_replace(
            '/(^|\s)@(\w+)/',
            '\1@<a href="http://twitter.com/\2" target="_blank">\2</a>',
            $text
        );

        // linkify tags
        $text = preg_replace(
            '/(^|\s)#(\w+)/',
            '\1#<a href="http://search.twitter.com/search?q=%23\2" target="_blank">\2</a>',
            $text
        );

        return $text;
    }
}