
# Lstarter

**Lstarter** is a Laravel package designed to simplify the process of generating a `layout.blade.php` file with Bootstrap 5.3 integration. The package includes prepacked layouts that can be easily copied to your Laravel application's `resources/views/layouts` directory.

## Installation

To install the Lstarter package, you can use Composer:

```bash
composer require jiten14/lstarter
```

## Usage

Once the package is installed, you can generate the layout file using the following Artisan command:

```bash
php artisan generate:layout
```

### What the Command Does

- Copies the `layout.blade.php` file from the package's vendor directory to the `resources/views/layouts` directory of your Laravel application.
- The layout includes Bootstrap 5.3 integration and sections for displaying success and error messages.

## Layout Overview

The generated `layout.blade.php` file includes:

- **Bootstrap 5.3 Integration**: The layout is styled using Bootstrap 5.3, providing a responsive and modern design.
- **Session Messages**: It includes sections to display error and success messages using Laravel's session handling.

Here is an example of how the generated layout looks:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Layout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Content -->
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

## Customization

After generating the layout file, you can customize it as needed for your application. The file is located in the `resources/views/layouts` directory of your Laravel project.

## Contribution

If you'd like to contribute to this package:

1. Fork the repository on GitHub.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes and commit them (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Create a Pull Request on GitHub.

## Versioning

Lstarter follows semantic versioning:

- **v1.0.0**: Initial release.
- **v1.0.1**: Minor Fixes & refactor the code.
- **v1.1.0**: Upcoming features or improvements will be released in this version.

### How to Update

To update the package to a newer version:

```bash
composer update jiten14/lstarter
```

### Downgrading

If you need to revert to a previous version:

```bash
composer require jiten14/lstarter:1.0.0
```

## Support & Contact

If you encounter any issues or bugs, or if you need support with this package, feel free to reach out. I am happy to help you!

**Author**: Jitendriya Tripathy  
**Email**: [\[Jiten's email\]](mailto:jitendriya14@gmail.com)

## License

This package is open-source software licensed under the [MIT license](LICENSE).
