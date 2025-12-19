# GoogleReviews Bundle

A Symfony bundle to easily fetch Google reviews via the Google Places API. The result can be cached to avoid excessive API access. Review data is deserialized to a model for further use.

## Requirements

- PHP 8.2 or higher
- Symfony 6.4 or 7.x

## Installation

Install the package with:

```console
composer require manuxi/google-reviews-bundle
```

If you're *not* using Symfony Flex, you'll also need to add the bundle in your `config/bundles.php` file:

```php
Manuxi\GoogleReviewsBundle\ManuxiGoogleReviewsBundle::class => ['all' => true],
```

## Usage

```php
// src/Controller/MyController.php
use Manuxi\GoogleReviewsBundle\ManuxiGoogleReviews;

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

You can also access this service directly using the id `manuxi_google_reviews.google_reviews`.

You can modify the offset and the length of the reviews:

```php
$reviews = $googleReviews->getReviews(2, 3); // returns 3 elements, starting at offset 2
```

## Twig Usage

The extension comes with two Twig functions which will give you the overall count and the reviews themselves.

```twig
{% for review in get_google_reviews(1, 2) %}
    {{ dump(review) }}
{% endfor %}

Total reviews: {{ get_google_reviews_count() }}
```

## Configuration

To use this extension please refer to https://developers.google.com/maps/documentation/embed/get-api-key to get your API key.

You also need the CID of the specific business partner. To get it see here: https://www.sterlingsky.ca/how-to-find-the-cid-number-on-google-maps/.

Create a new `config/packages/manuxi_google_reviews.yaml` file:

```yaml
# config/packages/manuxi_google_reviews.yaml
manuxi_google_reviews:
    connector:
        # API key
        api_key: '%env(GOOGLE_PLACES_API_KEY)%'
        # CID
        cid: '%env(GOOGLE_PLACES_CID)%'
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
Please feel comfortable submitting issues or pull requests.
