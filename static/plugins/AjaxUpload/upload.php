<?php

if(count($_FILES) > 0)
{
	$f = $_FILES['file'];
	$filename = 'Upload/' . md5(uniqid(rand())) . '_' . $f['name'];
	move_uploaded_file($f['tmp_name'], $filename);
	echo 'Success';
}
else
{
	echo 'no files';
}

?>