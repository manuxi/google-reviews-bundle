# GoogleReviews Bundle

Ein Symfony Bundle zum einfachen Abrufen von Google-Bewertungen über die Google Places API. Das Ergebnis kann gecacht werden, um übermäßige API-Zugriffe zu vermeiden. Die Bewertungsdaten werden in ein Model deserialisiert.

## Voraussetzungen

- PHP 8.2 oder höher
- Symfony 6.4 oder 7.x

## Installation

Installiere das Paket mit:

```console
composer require manuxi/google-reviews-bundle
```

Wenn du Symfony Flex *nicht* verwendest, musst du das Bundle auch in deiner `config/bundles.php` Datei hinzufügen:

```php
Manuxi\GoogleReviewsBundle\ManuxiGoogleReviewsBundle::class => ['all' => true],
```

## Verwendung

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

Du kannst den Service auch direkt über die ID `manuxi_google_reviews.google_reviews` ansprechen.

Du kannst den Offset und die Länge der Bewertungen anpassen:

```php
$reviews = $googleReviews->getReviews(2, 3); // gibt 3 Elemente zurück, beginnend bei Offset 2
```

## Twig Verwendung

Die Erweiterung enthält zwei Twig-Funktionen für die Gesamtzahl und die Bewertungen selbst.

```twig
{% for review in get_google_reviews(1, 2) %}
    {{ dump(review) }}
{% endfor %}

Gesamtbewertungen: {{ get_google_reviews_count() }}
```

## Konfiguration

Um diese Erweiterung zu nutzen, hole dir einen API-Schlüssel unter https://developers.google.com/maps/documentation/embed/get-api-key.

Du benötigst auch die CID des jeweiligen Geschäftspartners. Siehe hier: https://www.sterlingsky.ca/how-to-find-the-cid-number-on-google-maps/.

Erstelle eine neue `config/packages/manuxi_google_reviews.yaml` Datei:

```yaml
# config/packages/manuxi_google_reviews.yaml
manuxi_google_reviews:
    connector:
        # API-Schlüssel
        api_key: '%env(GOOGLE_PLACES_API_KEY)%'
        # CID
        cid: '%env(GOOGLE_PLACES_CID)%'
        # Sprache, Standard ist en
        locale: de
    cache:
        # Caching empfohlen!
        enabled: true
        pool: cache.app
        ttl: 86400
```

## Mitwirken

Der Einfachheit halber wurde diese Erweiterung klein gehalten.
Issues oder Pull Requests sind willkommen.
