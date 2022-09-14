<?php 
	 $errors= array();
      $file_name = $_FILES['audio-blob']['name'];
      $file_size =$_FILES['audio-blob']['size'];
      $file_tmp =$_FILES['audio-blob']['tmp_name'];
      $uploadFileDir = './uploads/';
      $dest_path = $uploadFileDir . $file_name.'.webm';
   
       move_uploaded_file($file_tmp, $dest_path);

?>