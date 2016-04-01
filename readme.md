# SocialFeed

Get feeds from different social networks in a unified format.

## Install

Use composer: `composer require jessicadigital/social-feed`

## Features

- Get the most recent posts of a user
- Get a specific item based on an ID
- Implemented media: Facebook, Instagram, Pinterest, Twitter, YouTube
- Returns items in simple format
- Gets information (id, service, thumbnail) on attached video/images from: youtube, vine, instagram, vimeo
- Get an id from an url of a post

## Example

### Twitter

```php
$twitter = new JessicaDigital\SocialFeed\Services\TwitterService(array(
  'access_token' => 'xxx',
  'access_token_secret' => 'xxx',
  'consumer_key' => 'xxx',
  'consumer_secret' => 'xxx'
));
$feed = $twitter->getFeed('jessica_digital');
```

## Credentials

The following credentials are needed depending on the medium:
- Facebook: app_id, app_secret
- Instagram: client_id, client_secret
- Pinterest: [none]
- Twitter: consumer_key, consumer_secret, access_token, access_token_secret
- Youtube: api_key
