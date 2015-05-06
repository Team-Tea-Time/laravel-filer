**Note: this package is under active development. While it should be completely functional, caution is advised and you might wish to wait until the first release is pushed out. Until then, things are likely to change and possibly break as features are added or altered.**

If you encounter any issues or have a suggestion, please [create an issue](https://github.com/Team-Tea-Time/laravel-filer/issues/new).

Please also be aware that this package **is not** designed to handle uploading, filesystem operations (such as move/delete) or image manipulation. It's simply designed to compliment other packages and tools that already exist in Laravel. If you're looking for a more feature-complete attachments solution, take a look at [CodeSleeve/stapler](https://github.com/CodeSleeve/stapler).

## Installation

### Step 1: Install the package

Add the package to your composer.json and run `composer update`:

```
"teamteatime/laravel-filer": "dev-master"
```

Add the service provider to your `config/app.php`:

```php
'TeamTeaTime\Filer\FilerServiceProvider',
```

> If your app defines a catch-all route, make sure you load this service provider before your app service providers.

### Step 2: Publish the package files

Run the vendor:publish command to publish Filer's migrations:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

### Step 4: Update your models

Add attachment support to your models by using AttachableTrait:

```php
class ... extends Eloquent {
    use \TeamTeaTime\Filer\AttachableTrait;
}
```

## Configuration

Filer requires no configuration out of the box in most cases, but the following options are available to you in `config/filer.php`:

Option | Type | Description | Default
------ | ---- | ----------- | -------
path | Array | Contains the relative and absolute paths to the directory where your attachment files are stored. | uploads
append_querystring | Boolean | If enabled, attachment URLs include a querystring containing the attachment's updated_at timestamp. This prevents out of date attachments from being loaded by the browser. | true
user | Array | The name of your app's User model, and a closure to return the user ID. These are used to associate attachments with users. | Auth::user()->id or 0

## Usage

To attach a file or URL, use the `attach()` method on your model. This method will accept any of the following:

...a **local file path**
```php
$user->attach('uploads/avatars/1.jpg');
```

...an instance of **SplFileInfo** (or `Symfony\Component\HttpFoundation\File\File`)
```php
$photo = Request::file('photo')->move($destinationPath);
$user->attach($photo);
```

...or a **URL**
```php
$user->attach('http://www.analysis.im/uploads/seminar/pdf-sample.pdf');
```

You can also specify a key, title, and/or description using the options array:

```php
$user->attach('uploads/avatars/1.jpg', ['key' => 'avatar']);
```

```php
$article->attach($pdf, ['title' => "Event 2015 Guide", 'description' => "The complete guide for this year's event."]);
```

By default, attachments are associated with user IDs using the closure specified in the `filer.user.id` config option. You can override this config option to return any integer, or override the value used at call time:

```php
$user->attach($photo, ['user_id' => $user->id]);
```

Depending on what you pass to this method, the item will be stored as either a `TeamTeaTime\Filer\LocalFile` or a `TeamTeaTime\Filer\URL`. You can later call on attachments via the `attachments` relationship. Examples are provided below.

### Displaying a list of attachments in a view

```
@foreach ($article->attachments as $attachment)
<a href="{{ $attachment->url }}">{{ $attachment->title }}</a>
<p class="description">{{ $attachment->description }}</p>
@endforeach
```

### Retrieving a specific attachment

```php
$user->attachments()->find($attachment_id);
```

### Retrieving an attachment by key

```php
$user->attachments()->key('avatar')->first();
```

### Accessing an attachment's properties and type-specific properties

```php
$avatar = $user->attachments()->key('avatar')->first();
$avatar->url;               // the URL to the file
$avatar->title;             // the attachment title, if any
$avatar->description;       // the attachment description, if any

// If the attachment is a LocalFile...
$avatar->downloadURL;       // the URL to download the file
$avatar->item->filename;    // the filename, with its extension
$avatar->item->path;        // the path to the directory where the file exists
$avatar->item->mimetype;    // the file's detected mimetype
$avatar->item->size;        // the file size, in bytes
```

### Generating a download link

You can use the `downloadURL` attribute as shown above, or construct the route using a file ID:

```php
route('filer.file.download', $file_id)
```
