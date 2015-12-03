<?php

namespace Shrayyef\File;

use Shrayyef\Storage\Storage;

class File
{

	/**
     * File upload Errors
     *
     * @var array
     */
	protected $errors = [];

	/**
     * PHP $_FILES error codes
     *
     * @var array
     */
	protected $uploadErrors = [
		'UPLOAD_ERR_OK',
		'UPLOAD_ERR_INI_SIZE',
		'UPLOAD_ERR_FORM_SIZE',
		'UPLOAD_ERR_PARTIAL',
		'UPLOAD_ERR_NO_FILE',
		'UPLOAD_ERR_NO_TMP_DIR',
		'UPLOAD_ERR_CANT_WRITE',
		'UPLOAD_ERR_EXTENSION'
	];

	/**
     * upload directory path
     *
     * @var string
     */
	protected $path;

	/**
     * uploaded image path
     *
     * @var string
     */
	public $imagePath;

	/**
     * $_FILES fields
     *
     * @var array
     */
	protected $file = [];

	/**
     * validation
     *
     * @var array
     */
	protected $validation = [];

	/**
     * store valid files
     *
     * @var array
     */
	protected $validFiles = [];

	/**
		 * store invalid files
		 *
		 * @var array
		 */
	protected $inValidFiles = [];

  /**
   * if validation passes mark as true.
   *
   * @var bool
   */
  protected $passes = false;


	/**
   * create $file array which holds $_FILES content.
   *
   * @return array
   */
	public function __construct($upload_name, $unique)
	{
		$this->validation['dublicate'] = $unique;
		if (is_array($_FILES[$upload_name]['name'])) {
			foreach ($this->diverse_array($_FILES[$upload_name]) as $key => $value) {
				$this->file[$key] = $value;
			}
		} else {
			foreach ($_FILES[$upload_name] as $key => $value) {
				$this->file[$key] = $value;
			}
		}
	}

	/**
     * set upload path attribute.
     *
     * @return void
     */
	public function uploadPath($path)
	{
		$this->path = $path;
	}

	/**
     * set upload path attribute.
     *
     * @return void
     */
	public function imagePath($fileName)
	{
		return $this->path . '/' . $fileName;
	}

    /**
     * add type validation to file upload.
     *
     * @return void
     */
	public function validType(array $allowed)
	{
		$this->validation['type'] = $allowed;
	}

    /**
     * add size validation to file upload.
     *
     * @return void
     */
	public function validSize($size)
	{
		$this->validation['size'] = $this->convertSize($size);
	}

	public function validate()
	{
		if (!empty($this->validation)) {
			$validFile = [];
			foreach ($this->validation as $key => $value) {
				switch ($key) {
					case 'type':
						$finfo = new \finfo(FILEINFO_MIME);
						$finfoType = $finfo->file($this->file['tmp_name']);

						$finfoTypeArray = explode(';', $finfoType);

						$type = $finfoTypeArray[0];

						if (!in_array($this->file['type'], $value) && !in_array($type, $value)) {
							$this->addError('type', 'file type ' . $this->file['type'] . ' is invalid.');
							unset($this->file);
							break 2;
						}
					break;

					case 'size':
						if ($this->file['size'] > $value) {
							$this->addError('size', 'file size must be smaller than ' . $this->convertSize($value));
							unset($this->file);
							break 2;
						}
					break;

					case 'dublicate':
						if ($value) {
							if (is_file($this->path . '/' . $this->file['name'])) {
								$this->addError('dublicate', 'file with this name already exists.');
								unset($this->file);
								break 2;
							}
						}
					break;
				}
			}
		}
	}

	public function validateFiles()
	{
		if (!empty($this->validation)) {
			foreach ($this->file as $fileKey => $fileArray) {
				foreach ($this->validation as $validKey => $validValue) {
					$valid = false;
					switch ($validKey) {
						case 'type':
							$finfo = new \finfo(FILEINFO_MIME);
							$finfoType = $finfo->file($fileArray['tmp_name']);

							$finfoTypeArray = explode(';', $finfoType);

							$type = $finfoTypeArray[0];

							if (!in_array($fileArray['type'], $validValue) && !in_array($type, $validValue)) {
								$fileArray['validate_error'] = 'file type is invalid.';
								$this->addInvalidFile($fileArray);
								unset($this->file[$fileKey]);
								break 2;
							}
						break;
						case 'size':
							if ($fileArray['size'] > $validValue) {
								$fileArray['validate_error'] = 'file size must be smaller than ' . $this->convertSize($validValue);
								$this->addInvalidFile($fileArray);
								unset($this->file[$fileKey]);
								break 2;
							}
						break;
						case 'dublicate':
							if ($validValue) {
								if (is_file($this->path . '/' . $fileArray['name'])) {
									$fileArray['dublicate'] = 'file with this name already exists.';
									$this->addInvalidFile($fileArray);
									unset($this->file[$fileKey]);
									break 2;
								}
							}
						break;
					}
				}
			}
		}
	}

	/**
	 * move uploaded files from tmp directory valid files.
	 *
	 * @return void
	 */
	public function upload()
	{
		if(!is_dir($this->path)) {
			$storage = new Storage();
			$storage->folder($this->path);
		}

		if (!isset($this->file['name'])) {
			foreach ($this->file as $key => $value) {
					move_uploaded_file($value['tmp_name'], $this->path . '/' . preg_replace('/\s+/', '', $value['name']));
			}
		} else {
			if(empty($this->errors)) {
				move_uploaded_file($this->file['tmp_name'], $this->path . '/' . preg_replace('/\s+/', '', $this->file['name']));
			}
		}
	}

    /**
     * add error to $this->errors.
     *
     * @return void
     */
	public function addError($key, $value)
	{
		$this->errors[$key] = $value;
	}

	/**
	 * add valid file to $this->validFiles array.
	 *
	 * @return void
	 */
	protected function addValidFile($file)
	{
		$this->validFiles[] = $file;
	}

	/**
	 * add invalid file to $this->inValidFiles array.
	 *
	 * @return void
	 */
	protected function addInValidFile($file)
	{
		$this->inValidFiles[] = $file;
	}

	/**
	 * check if there are valid files
	 *
	 * @return bool
	 */
	public function passes()
	{
		if (!empty($this->file) && empty($this->errors)) {
			return true;
		}
	}

	/**
	 * return value of $this->errors array.
	 *
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * return valid files array.
	 *
	 * @return array
	 */
	public function validFiles()
	{
		return $this->file;
	}

	/**
	 * return invalid files array.
	 *
	 * @return array
	 */
	public function inValidFiles()
	{
		return $this->inValidFiles;
	}

    /**
     * convert size from number to text and so forth.
     *
     * @return mixed
     */
	public function convertSize($size)
	{
		$currentLetter = substr($size, -2);
		$letters = ['TB', 'GB', 'MB', 'KB'];

		$sizeInBytes = str_replace($letters, '', $size);

		switch ($currentLetter) {
			case 'TB':
				return $sizeInBytes * 1099511627775.9133;
			break;

			case 'GB':
				return $sizeInBytes * 1073741824;
			break;

			case 'MB':
				return $sizeInBytes * 1048576;
			break;

			case 'KB':
				return $sizeInBytes * 1024;
			break;

			default:

				if($sizeInBytes < 1024) {
					return "{$sizeInBytes} bytes";
				} elseif($sizeInBytes < 1048576) {
					$size_kb = round($sizeInBytes/1024);
					return "{$size_kb} KB";
				} else {
					$size_mb = round($sizeInBytes/1048576, 1);
					return "{$size_mb} MB";
				}
			break;
		}
	}

	protected function diverse_array($array)
	{
		$result = array();
    foreach($array as $key1 => $value1)
        foreach($value1 as $key2 => $value2)
            $result[$key2][$key1] = $value2;
    return $result;
	}

}
