# Spiral Validator

[![PHP](https://img.shields.io/packagist/php-v/spiral/validator.svg?style=flat-square)](https://packagist.org/packages/spiral/validator)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/spiral/validator.svg?style=flat-square)](https://packagist.org/packages/spiral/validator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spiral/validator/run-tests?label=tests&style=flat-square)](https://github.com/spiral/validator/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/spiral/validator.svg?style=flat-square)](https://packagist.org/packages/spiral/validator)

The component provides an array-based DSL to construct complex validation chains.

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework 3.0+

## Installation

You can install the package via composer:

```bash
composer require spiral/validator
```

After package install you need to register bootloader from the package.

```php
protected const LOAD = [
    // ...
    \Spiral\Validator\Bootloader\ValidatorBootloader::class,
];
```

> Note: if you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Example of usage

```php
<?php

declare(strict_types=1);

namespace App\Filters;

use Spiral\Filters\Model\FilterInterface;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;
use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\Input\File;

class CreatePostFilter implements FilterInterface, HasFilterDefinition
{
    #[Post(key: 'title')]
    public string $title;
    
    #[Post(key: 'text')]
    public string $text;
    
    #[File]
    public UploadedFile $image;
    
    // ...

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition(
            validationRules: [
                'title' => [
                    ['notEmpty'],
                    ['string::length', 50]
                ],
                'text' => [['notEmpty']],
                'image' => [['image::valid'], ['file::size', 1024]]
                
                // ...
            ]
        );
    }
}
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
