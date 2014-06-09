<?php
class Batch extends DA_Controller{
	
	function __construct(){
		parent::__construct();
	}
	
	function setProfilePicStructure(){
		$this->load->model('userMemberModel');
		$userMemberList = $this->userMemberModel->getAll();
		$folderList = $this->listFolderFiles();
		foreach ($userMemberList as $userMember){
			if(!in_array($userMember->id, $folderList)){
				mkdir($_SERVER['DOCUMENT_ROOT']."/images/user-avatars/".$userMember->id, 0700);
				$newfile = $_SERVER['DOCUMENT_ROOT']."/images/user-avatars/".$userMember->id."/".$userMember->id."_profilepic.png";
				$file = $_SERVER['DOCUMENT_ROOT']."/images/default_avatar_1.png";
				if (!copy($file, $newfile)) {
					echo "failed to copy $file...\n";
				}
				
				$newfile = $_SERVER['DOCUMENT_ROOT']."/images/user-avatars/".$userMember->id."/".$userMember->id."_thumb.png";
				$file = $_SERVER['DOCUMENT_ROOT']."/images/default_avatar_thumb_1.png";
				if (!copy($file, $newfile)) {
					echo "failed to copy $file...\n";
				}
				
				$profilePic = "/images/user-avatars/".$userMember->id."/".$userMember->id."_profilepic.png";
				//echo $profilePic;
				$this->userMemberModel->updateUserProfilePic($userMember->id,$profilePic);
			} else{
				$dir = $_SERVER['DOCUMENT_ROOT']."/images/user-avatars/";
				$dirName = $userMember->id;
                echo $userMember->id."<br/>";
				if ($handle = opendir($dir."/".$dirName)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != "..") {
							if($entry != 'Thumbs.db' && $entry != '.svn'){
								$file = $dir.$dirName."/".$entry;
								$extArray = explode(".",$entry);
								$newfile = $dir.$dirName."/".$dirName."_thumb.".$extArray[1];
								//echo $newfile."<br/>";
								if (!copy($file, $newfile)) {
									//echo "failed to copy $file...\n";
								}
								$image_location = $file;
								$image_size = getimagesize($image_location);
								$width = $image_size[0];
								$height = $image_size[1];
								$scale = $width / $height;
								if($scale > 1){
									$scale = 180 / $height;
								}else{
									$scale = 180 / $width;
								}
								$this->imageResize($image_location, $width, $height, $scale);
								 
								 
								$image_location = $newfile;
								$image_size = getimagesize($image_location);
								$width = $image_size[0];
								$height = $image_size[1];
								$scale = $width / $height;
								if($scale > 1){
									$scale = 35 / $height;
								}else{
									$scale = 35 / $width;
								}
								$this->imageResize($image_location, $width, $height, $scale);
							}
						}
					}
					closedir($handle);
				}
			}
		}
	}
	
	function listFolderFiles(){
		$dir = $_SERVER['DOCUMENT_ROOT']."/images/user-avatars/";
	    $ffs = scandir($dir);
	    $dirArray = array();
	    foreach($ffs as $ff){
	        if($ff != '.' && $ff != '..' && $ff != '.svn'){
	            array_push($dirArray, $ff);
	            
	        }
	    }
	    return $dirArray;
	}
	
	function imageResize($image,$width,$height,$scale){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		$newImageHeight = ceil($height * $scale);
		$newImageWidth = ceil($width * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image);
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image);
				break;
			case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image);
				break;
		}
		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

		switch($imageType) {
			case "image/gif":
				imagegif($newImage,$image);
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				imagejpeg($newImage,$image,90);
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$image);
				break;
		}

		chmod($image, 0777);
		return $image;
	}
}