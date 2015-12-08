# FileUpload
php file upload library.

## Install
run composer install in the downloaded folder.

## Basic usage

```html
      <form method="post" enctype="multipart/form-data">
          <input type="file" name="file">
          <input type="submit" name="submit" value="upload">
      </form>
```

#Usage
### Multi file upload

```php
require_once '../vendor/autoload.php';

use Shrayyef\File\File;

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

 
