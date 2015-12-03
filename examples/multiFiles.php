<?php
require_once '../vendor/autoload.php';

use Shrayyef\File\File;

  if(isset($_POST['submit'])) {

    $file = new File('files', true);
    $file->validType(array('image/jpg', 'image/jpeg'));
    $file->validSize('5MB');

    $file->uploadPath(dirname(__DIR__) . '/dir');

    $file->validateFiles();

    $file->upload();

    var_dump($file->validFiles());

    echo '<hr>';

    var_dump($file->inValidFiles());

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Multi files upload</title>
  </head>
  <body>
    <div class="container">
      <h2>Multi files upload</h2>
      <form action="multiFiles.php" method="post" enctype="multipart/form-data">
        <div class="input">
          <label for="files">Select image to upload:</label>
          <input type="file" name="files[]" id="files" multiple>
        </div>
        <div class="input">
          <label for="submit">&nbsp;</label>
          <input type="submit" name="submit" value="upload">
        </div>
      </form>
    </div>
  </body>
</html>
