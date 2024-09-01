
# Lstarter

**Lstarter** is a Laravel package designed to streamline the process of generating migrations, models, relationships, factories, seeders, and controllers all at once, in addition to copying a `layout.blade.php` file with Bootstrap 5.3 integration. The package includes prepacked layouts that can be easily copied to your Laravel application's `resources/views/layouts` directory.This package simplifies the development process, allowing you to quickly set up essential parts of your Laravel application.

## Installation

To install the Lstarter package, you can use Composer:

```bash
composer require jiten14/lstarter
```

## Usage

### Generate All at Once

After installing the package, you can generate the migration, model, relationships, factory, seeder, and controller files all at once by running the following Artisan command:

```bash
php artisan generate:package
```
### Generate Layout

To generate and copy the layout.blade.php file, which includes Bootstrap 5.3 integration, run the following command:

```bash
php artisan generate:layout
```

## What the Command Does

### generate:package

- Creates a migration file in the `database/migrations` directory.
- Generates a model with appropriate relationships in the `app/Models` directory.
- Sets up the factory and seeder classes in the `database/factories` and `database/seeders` directories, respectively.
- Generates a controller file in the `app/Http/Controllers` directory.

### generate:layout

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

Although the generate:package command will generate the migration, model, relationships, factory, seeder, and controller files with prefilled data, you can also customize these files to suit your specific requirements. The generated files are located in the following directories:

- Migrations: `database/migrations`
- Models: `app/Models`
- Controllers: `app/Http/Controllers`
- Factories: `database/factories`
- Seeders: `database/seeders`

You can modify the code within these files to add custom fields, validation rules, relationships, or any other specific logic needed for your application. After generating the layout file, you can customize it as needed for your application. The file is located in the `resources/views/layouts` directory of your Laravel project.

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
- **v1.1.0**: Added features to Generate Migration, Model, Controller, Factory & Seeder.
- **v1.2.0**: Upcoming features or improvements will be released in this version.

### How to Update

To update the package to a newer version:

```bash
composer update jiten14/lstarter
```

### Downgrading

If you need to revert to a previous version:

```bash
composer require jiten14/lstarter:1.0.1
```

## Support & Contact

If you encounter any issues or bugs, or if you need support with this package, feel free to reach out. I am happy to help you!

**Author**: Jitendriya Tripathy  
**Email**: [Jiten's email](mailto:jitendriya14@gmail.com)

## License

This package is open-source software licensed under the [MIT license](LICENSE).
