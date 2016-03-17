<?php
namespace JessicaDigital\SocialFeed;

class Config {
    public function __construct(Array $cfg = []) {
        foreach ($cfg as $k => $v) {
            if (isset($this->{$k})) {
                $this->{$k} = $v;
            }
        }
    }
    /** @var bool */
    public $create_hash = false;
}
