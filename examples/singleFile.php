<?php
require_once '../vendor/autoload.php';

use Shrayyef\File\File;

  if(isset($_POST['submit'])) {

    $file = new File('file', true);
    $file->validType(array('image/jpg', 'image/jpeg'));
    $file->validSize('5MB');

    $file->uploadPath(dirname(__DIR__) . '/files');

    $file->validate();


    if ($file->passes()) {
      $file->upload();
    } else {
      var_dump($file->errors());
    }

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>single file upload</title>
  </head>
  <body>
    <div class="container">
      <h2>single file upload</h2>
      <form action="singleFile.php" method="post" enctype="multipart/form-data">
        <div class="input">
          <label for="file_upload">select file to upload</label>
          <input type="file" name="file">
        </div>
        <div class="input">
          <label for="submit">&nbsp;</label>
          <input type="submit" name="submit" value="upload">
        </div>
      </form>
    </div>
  </body>
</html>
