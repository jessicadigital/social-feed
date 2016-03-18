<?php

namespace JessicaDigital\SocialFeed\Items;

use JessicaDigital\SocialFeed\Items\User;
use JessicaDigital\SocialFeed\Media\Facebook;

class FacebookItem extends Item {
    protected $comments = 0;
    protected $likes = 0;
    public $service = 'facebook';
    
    public function __construct($data) {
        
        $this->id = $data->id;
        $this->comments = $data->comments->summary->total_count;
        $this->created = strtotime($data->created_time);
        $this->likes = $data->likes->summary->total_count;
        $this->link = 'http://www.facebook.com/permalink.php?id='.$data->from->id.'&v=wall&story_fbid='.$data->id;
        $this->livetext = $this->linkify($data->message);
        $this->text = $data->message;
        
        $user = new User();
        $user->id = $data->from->id;
        $user->image = 'https://graph.facebook.com/v2.3/'.$data->from->id.'/picture/';
        $user->link = 'https://facebook.com/profile.php?id='.$data->from->id;
        $user->name = $data->from->name;
        $this->user = $user;
        
        if (!empty($data->picture)) {
            $this->media = new Facebook($data->picture);
        }
    }
    
    public function linkify($text) {
        // linkify URLs
        $text = preg_replace(
            '/(https?:\/\/\S+)/',
            '<a href="\1" target="_blank">\1</a>',
            $text
        );

        // linkify tags
        $text = preg_replace(
            '/(^|\s)#(\w+)/',
            '\1#<a href="https://www.facebook.com/hashtag/\2" target="_blank">\2</a>',
            $text
        );

        return $text;
    }
}