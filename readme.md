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
current_user | Closure | Returns your app's current user object. This is used to associate attachments with their owners. | Auth::user()
append_querystring | Boolean | If enabled, attachment URLs include a querystring containing the attachment's updated_at timestamp. This prevents out of date attachments from being loaded by the browser. | true

## Usage

To attach a file or URL, use the `attach()` method on your model. This method will accept a **local file path**, an instance of **SplFileInfo** (or `Symfony\Component\HttpFoundation\File\File`) or a **URL**.

```php
$user->attach('uploads/avatars/1.jpg')
```

Optionally, you can specify a key. Keys can be used to identify an attachment or create sub-sets of attachments using a custom string.

```php
$user->attach('uploads/avatars/1.jpg', 'avatar')
```

You can also give the attachment a title and description:

```php
$article->attach('uploads/event2015.pdf', '', "Event 2015 Guide", "The complete guide for this year's event.")
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
$user->attachments()->where(['id' => $attachment_id])
```

### Retrieving an attachment by key

```php
$user->attachments()->key('avatar')->first()
```

### Accessing an attachment's properties and type-specific properties

```php
$avatar = $user->attachments()->key('avatar')->first();
$avatar->url;               // the URL to the file
$avatar->title;             // the attachment title, if any
$avatar->description;       // the attachment description, if any

// If the attachment is a LocalFile...
$avatar->downloadURL;       // the download URL to the file (returns a download response to the browser)
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
