# fileUpload
php file upload library 

## Basic usage

```html
      <form method="post" enctype="multipart/form-data">
          <input type="file" name="file">
          <input type="submit" name="submit" value="upload">
      </form>
```

```php
require_once '../vendor/autoload.php';

use Shrayyef\File\File;

```

### single file upload

```php
$file = new File('file', true); // true will check if file exist before uploading
$file->validType(array('image/jpg', 'image/jpeg')); // you can specify any type you want
$file->validSize('5MB');

$file->uploadPath(dirname(__DIR__) . '/files');
$file->validate();

if ($file->passes()) {
   $file->upload();
} else {
   var_dump($file->errors());   
}

```

### multi file upload

```php
$file = new File('file', true); // true will check if file exist before uploading
$file->validType(array('image/jpg', 'image/jpeg')); // you can specify any type you want
$file->validSize('5MB');

$file->uploadPath(dirname(__DIR__) . '/files');
$file->validateFiles();

$file->upload();

var_dump($file->validFiles());

echo '<hr>';

var_dump($file->inValidFiles());

```

 