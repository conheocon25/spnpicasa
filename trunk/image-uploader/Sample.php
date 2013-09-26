<?php
	include 'Loader.php';
    \ChipVN\Loader::registerAutoLoad();
	
	//Khởi tạo lớp dịch vụ
	$uploader = \ChipVN\Image_Uploader::factory('Picasa');
    $uploader->login('quanlycafevinhlong@gmail.com', 'admin123456789');
    
	//Gán Album và up lên 1 ảnh đã được chọn.
    //$uploader->setAlbumId('5927841217528180033');
    //echo $uploader->upload('test.png');
	
	//Tạo Album và up mới 1 ảnh vào album
	$IdAlbum = $uploader->addAlbum("Cafe Cõi Riêng");	
	$uploader->setAlbumId($IdAlbum);
    $uploader->upload('test.png');
	
	//Cập nhật Album
	
	//Xóa Album
	
?>