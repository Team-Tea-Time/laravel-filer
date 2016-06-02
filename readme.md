Please be aware that this package **is not** designed to handle uploading or image manipulation. It's simply designed to compliment other packages and tools that already exist in Laravel. If you're looking for a more feature-complete attachments solution, take a look at [CodeSleeve/stapler](https://github.com/CodeSleeve/stapler).

## Installation

### Step 1: Install the package

Install the package via Composer:

```
composer require teamteatime/laravel-filer
```

Add the service provider to your `config/app.php`:

```php
TeamTeaTime\Filer\FilerServiceProvider::class,
```

> If your app defines a catch-all route, make sure you load this service provider before your app service providers.

### Step 2: Publish the package files

Run the vendor:publish command to publish Filer's migrations:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

### Step 4: Update your models

Add attachment support to your models by using the HasAttachments trait:

```php
class ... extends Eloquent {
    use \TeamTeaTime\Filer\HasAttachments;
}
```

## Configuration

Filer requires no configuration out of the box in most cases, but the following options are available to you in `config/filer.php`:

Option | Type | Description | Default
------ | ---- | ----------- | -------
routes | Boolean | Determines whether or not to automatically define filer's routes. If you set this to `false`, you can optionally use `\TeamTeaTime\Filer\Filer::routes($router, $namespace)` in your routes.php. | true
route_prefix | string | If routes are enabled, this is used for all route prefixes. | files
hash_routes | Boolean | Enables unique hashes for local files to obfuscate their IDs in routes. | false
hash_length | string | The length to use when generating hashes for local files. | 40
path | Array | Contains the relative and absolute paths to the directory where your attachment files are stored. | storage_path('uploads')
append_querystring | Boolean | If enabled, attachment URLs include a querystring containing the attachment's updated_at timestamp. This prevents out of date attachments from being loaded by the browser. | true
cleanup_on_delete | Boolean | If enabled, Filer will attempt to delete local files referenced by deleted attachments. | true

## Usage

To attach a file or URL, use the `attach()` method on your model. This method will accept any of the following:

...a **local file path**
```php
$user->attach('avatars/1.jpg'); // path relative to your configured storage directory
```

...an instance of **SplFileInfo**
```php
$photo = Request::file('photo')->move($destinationPath);
$user->attach($photo);
```
> `Symfony\Component\HttpFoundation\File\File`, `Symfony\Component\HttpFoundation\File\UploadedFile` and `Illuminate\Http\UploadedFile` are extensions of `SplFileInfo` and Laravel Requests contain the latter by default.

...or a **URL**
```php
$user->attach('http://www.analysis.im/uploads/seminar/pdf-sample.pdf');
```

You can also specify a key (which uniquely identifies the attachment), a title, and/or a description using the options array:

```php
$user->attach('uploads/avatars/1.jpg', ['key' => 'avatar']);
```

```php
$article->attach($pdf, ['title' => "Event 2015 Guide", 'description' => "The complete guide for this year's event."]);
```

By default, attachments are associated with user IDs using `Auth::id()`. You can override this at call time:

```php
$user->attach($photo, ['user_id' => $user->id]);
```

```php
$article->attach($pdf, ['user_id' => null]);
```

Depending on what you pass to this method, the item will be stored as either a `TeamTeaTime\Filer\LocalFile` or a `TeamTeaTime\Filer\Url`. You can later call on attachments via the `attachments` relationship. Examples are provided below.

### Displaying a list of attachments in a view

```
@foreach ($article->attachments as $attachment)
<a href="{{ $attachment->getDownloadUrl() }}">{{ $attachment->title }}</a>
<p class="description">{{ $attachment->description }}</p>
@endforeach
```

### Retrieving an attachment by ID or key

```php
$user->attachments()->find($attachmentId);
```
```php
$user->findAttachmentByKey('avatar');
```

### Accessing an attachment's properties and type-specific properties

```php
$avatar = $user->findAttachmentByKey('avatar');
$avatar->getUrl();          // the URL to the file (see below)
$avatar->getDownloadUrl();  // the download URL to the file (see below)
$avatar->title;             // the attachment title, if any
$avatar->description;       // the attachment description, if any

// If the attachment is a LocalFile...
$avatar->item->filename;    // the filename, with its extension
$avatar->item->path;        // the path to the directory where the file exists
$avatar->item->mimetype;    // the file's detected MIME type
$avatar->item->size;        // the file size, in bytes
$avatar->item->getFile();   // the Symfony File representation of the file
$avatar->item->hash;        // the unique hash generated for the file (if filer.hash_routes is enabled)
```

### Generating URLs

The `getUrl()` and `getDownloadUrl()` methods above will return different values based on the attachment type; if it's a local file, they will return the 'view' and 'download' routes respectively, otherwise they'll return the URL that was attached.

For local files, the provided routes can be generated with a file ID or hash:

```php
route('filer.file.view', $fileId);
```

```php
route('filer.file.download', $fileId)
```

> Note that depending on the file's MIME type, the browser may begin a download with both of these routes.
