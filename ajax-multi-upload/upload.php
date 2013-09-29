<?php
if($_POST["action"]=="upload")
{
	if(!empty($_FILES))
	{
		$result = array();
		$tempFile = $_FILES["Filedata"]["tmp_name"];
		$targetPath = $_POST["path"];
		//$fileExt = strtolower(substr($_FILES['Filedata']['name'], strrpos($_FILES['Filedata']['name'], ".") + 1));
		//if($fileExt!="php" && $fileExt!="html" && $fileExt!="htm" && $fileExt!="js")//if you want to prevent from upload the files with given extensions uncomment this block
		//{//if you want to prevent from upload the files with given extensions uncomment this block		
		$targetFile =  $targetPath . stripslashes($_FILES["Filedata"]["name"]);
		move_uploaded_file($tempFile, $targetFile);
	}
}
?>