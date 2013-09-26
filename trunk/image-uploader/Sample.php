<?php
	include 'Loader.php';
    \ChipVN\Loader::registerAutoLoad();
	
	$uploader = \ChipVN\Image_Uploader::factory('Picasa');
    $uploader->login('chualongvien.vinhlong@gmail.com', 'admin123456');
    // you can set upload to an albumId by array of albums or an album, system will get a random album to upload 
    //$uploader->setAlbumId(array('51652569125195125', '515124156195725'));
    //$uploader->setAlbumId('51652569125195125');
    echo $uploader->upload('test.png');
?>