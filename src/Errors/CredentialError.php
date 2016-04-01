<?php
namespace JessicaDigital\SocialFeed\Errors;

class CredentialError extends \Exception {
    
    protected $credential = null;
    protected $service = null;
    
    public function __construct($service, $credential = 'unknown') {
        $this->credential = $credential;
        
        $this->message = sprintf('Missing credentials for service %s: %s', $this->service, $this->credential);
    }
}