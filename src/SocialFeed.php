<?php

namespace JessicaDigital\SocialFeed;

/**
 * Get feeds from different social networks in a unified format
 * @property-read TwitterService $twitter
 * @property-read SocialFeedService $facebook
 * @property-read SocialFeedService $instagram
 */
class SocialFeed {
    /** @var array */
	private $services = [];
    /** @var array */
	private $map = [];
    /** @var Config */
    private $config;

	public function __construct(Array $config = []) {
        $this->config = new Config($config);
		$this->registerService('twitter', 'JessicaDigital\\SocialFeed\\TwitterService');
		$this->registerService('facebook', 'JessicaDigital\\SocialFeed\\FacebookService');
		$this->registerService('instagram', 'JessicaDigital\\SocialFeed\\InstagramService');
	}

	/**
	 * @param $service
	 * @return SocialFeedService
	 * @throws \Exception
	 */
	public function __get($service) {
		if (isset($this->services[$service]))
			return $this->services[$service];

		if (!isset($this->map[$service]))
			throw new \Exception("Service not found: $service");

		$instance = new $this->map[$service]($this->config);
		if (!$instance instanceof SocialFeedService) {
			throw new \Exception("Service $service does not implement SocialFeedService");
		}

		return $this->services[$service] = $instance;
	}

	/**
	 * Add a new service
	 * @param string $service
	 * @param string $className
	 */
	public function registerService($service, $className) {
		$this->map[$service] = $className;
	}

	/**
	 * Get service by url
	 * @param $url
	 * @return null|string
	 */
	public function getServiceFromUrl($url) {
		if (strpos($url, 'facebook') > -1)
			return 'facebook';
		if (strpos($url, 'twitter') > -1)
			return 'twitter';
		if (strpos($url, 'instagram') > -1)
			return 'instagram';
		return null;
	}
}
