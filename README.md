# GoogleReviews!
I made this bundle to easily get Google reviews since I haven't found an extension which did this job well. Curl is used to grab data from the Google places api.
To avoid excessive api access, the result can be cached (see configuration options). 
The review data is deserialized to a model for further use.

Install the package with:
```console
composer require manuxi/google-reviews-bundle
```
If you're *not* using Symfony Flex, you'll also
need to add the bundle in your `config/bundles.php` file:
```
Manuxi\GoogleReviewsBundle\ManuxiGoogleReviewsBundle::class => ['all' => true],
```

## Usage
```php
// src/Controller/MyController.php
use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;
// ...
class MyController
{
    public function index(ManuxiGoogleReviews $googleReviews)
    {
        $reviews = $googleReviews->getReviews();
        $overallCount = $googleReviews->getReviewsCount();
        // ...
    }
}
```
You can also access this service directly using the id
`manuxi_google_reviews.google_reviews`.

You can modify the offset and the length of the reviews:
```php
// ...
$reviews = $googleReviews->getReviews(2, 3); //returns 3 elements, starting at offset 2
// ...
```

## Twig usage

The extension comes with two twig functions which will give you the overall count and the reviews themselves.
```twig
{% for review in get_google_reviews(1,2) %}
    {{ dump(review) }}
{% endfor %}
```

## Configuration
To use this extension please refer to https://developers.google.com/maps/documentation/embed/get-api-key to get your api key.
You also need the CID of the specific business partner. To get it see here: https://www.sterlingsky.ca/how-to-find-the-cid-number-on-google-maps/.

The extension is configured directly by creating a new `config/packages/manuxi_google_reviews.yaml` file. 
The mentioned api-key and cid must be placed here just as the default values:
```yaml
# config/packages/manuxi_google_reviews.yaml
manuxi_google_reviews:
    connector:
        # api-key
        api_key: 
        # CID
        cid: 
        # locale, defaults to en
        locale: en
    cache:
        # I suggest caching!
        enabled: true
        pool: cache.app
        ttl: 86400
```

## Contributing
For the sake of simplicity this extension was kept small.
It is tested only with my own tiny Google account (which has only 5 reviews in it). 
Please feel comfortable submitting issues or pull requests, I'd be glad to get feedback to improve the extension :).
