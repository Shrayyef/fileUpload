<?php

namespace Shrayyef\Storage;

class Storage
{
	public function folder($path, $mode = 0777)
	{
		return mkdir($path, $mode, true);
	}
}
