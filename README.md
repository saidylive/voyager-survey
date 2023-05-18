# Laravel voyager Survey

[![PHP version](https://d25lcipzij17d.cloudfront.net/badge.svg?id=ph&r=r&type=6e&v=1.0.1&x2=2)](https://packagist.org/packages/saidy/voyager-survey)

## Install

To install, run the following command

```plaintext
composer require saidy/voyager-survey
php artisan vendor:publish --provider="Saidy\VoyagerSurvey\Providers\VoyagerSurveyServiceProvider" 
```

## Sample Configuration

```plaintext
{
    "survey": {
        "page_title": "Trainee Information",
        "reference_slug": "trainee-info",
        "reference_column": "trainee_id",
        "reference_field": "trainee_id"
    }
}
```
