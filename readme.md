**See the [wiki](https://github.com/Team-Tea-Time/laravel-filer/wiki) for an overview of this project, goals and features, as well as documentation.**

## Installation

### Step 1: Install the package

Install the package via composer:

```
composer require teamteatime/laravel-filer
```

Then add the following service provider to your `config/app.php`:

```php
'TeamTeaTime\Filer\FilerServiceProvider',
```

### Step 2: Publish the package files

Run the vendor:publish command to publish Filer's migrations:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

### Step 4: Update your models

(TBD)
