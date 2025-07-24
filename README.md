# A Filament field that allows users to click and point to mark references on an image.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ruelluna/canvas-pointer.svg?style=flat-square)](https://packagist.org/packages/ruelluna/canvas-pointer)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ruelluna/canvas-pointer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ruelluna/canvas-pointer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ruelluna/canvas-pointer/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ruelluna/canvas-pointer/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ruelluna/canvas-pointer.svg?style=flat-square)](https://packagist.org/packages/ruelluna/canvas-pointer)

<img src="https://raw.githubusercontent.com/ruelluna/canvas-pointer/main/main.jpg" class="filament-hidden">

I have used it for a client that needs visual reprensentations to where the pains are located in the body. What is your use case? This field will produce a base-64 image and automatically convert it to a file stored in your configured storage disk, returning the URL to the image.

## Installation

You can install the package via composer:

```bash
composer require ruelluna/canvas-pointer
```


## Usage

```php
CanvasPointerField::make('body-points')
    ->pointRadius(15) // default is 5
    ->imageUrl('your image source')
    ->width(800) // required
    ->height(800) // required
    ->storageDisk('public') // default is 'public'
    ->storageDirectory('canvas-pointer') // default is 'canvas-pointer'
    ->label('Select body parts that are in pain'),
```

### Storage Configuration

By default, the component saves images to the 'public' disk in the 'canvas-pointer' directory. You can customize these settings using the `storageDisk()` and `storageDirectory()` methods.

#### Static Configuration

```php
CanvasPointerField::make('body-points')
    ->storageDisk('s3') // Use Amazon S3 storage
    ->storageDirectory('images/pointers') // Custom directory path
```

#### Dynamic Configuration

Both storage disk and directory can be set dynamically using closures:

```php
CanvasPointerField::make('body-points')
    ->storageDisk(fn () => config('filesystems.default')) // Use the default disk from config
    ->storageDirectory(function (Get $get) {
        // Use a different directory based on the user or other form data
        $userId = $get('user_id');
        return "users/{$userId}/pointers";
    })
```

This allows you to customize the storage location based on runtime conditions, such as the current user, tenant, or other form data.

### Image Creation Process

The Canvas Pointer field handles the image creation process automatically:

1. **Canvas Interaction**: When users click on the image, red dots are added to mark specific points.

2. **Base64 Image Generation**: After each interaction, the canvas (including the background image and all marked points) is converted to a base64-encoded image using Konva.js's `toDataURL()` method.

3. **Automatic File Conversion**: When the form is submitted, the component:
   - Detects that the field value is a base64 image
   - Removes the base64 prefix (`data:image/png;base64,`)
   - Decodes the base64 string to binary data
   - Generates a unique filename using UUID
   - Saves the image to the configured storage disk and directory
   - Returns the public URL of the saved file

4. **Database Storage**: Only the image URL is stored in your database, not the large base64 string, which prevents database size issues.

Example of the saved image URL:
```
https://yourdomain.com/storage/canvas-pointer/550e8400-e29b-41d4-a716-446655440000.png
```

This process happens automatically - you don't need to write any additional code to handle the conversion from base64 to file URL.

## Todos
- [ ] Automatically detect height and width
- [ ] Table Column Component
- [ ] Entry Component
- [ ] `npm` Konva dependency


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ruel Luna](https://github.com/ruelluna)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
