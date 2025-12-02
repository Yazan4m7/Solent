<?php 
$return["error"] = false;
$return["msg"] = "";
$return["success"] = false;
//array to return

if(isset($_FILES["file"])){
	if (!is_dir('files/' . $_POST['case_id'])) {
  	  mkdir('files/'. $_POST['case_id'], 0777, true);
	}

    //directory to upload file
    $target_dir =  'files/' . $_POST['case_id'] ; //create folder files/ to save file
    $filename = $_FILES["file"]["name"]; 
    //name of file
    //$_FILES["file"]["size"] get the size of file
    //you can validate here extension and size to upload file.

    $savefile = "$target_dir/$filename";
    //complete path to save file
    try {
    move_uploaded_file($_FILES["file"]["tmp_name"], $savefile);
    $return["msg"] = "Uploaded File to ". $savefile;
    $return["filePath"]=$savefile;
    $return["success"] = true;
    }
    catch(Exception $e) {
	$return["success"] = false;
        $return["msg"] = 'Message: ' .$e->getMessage();
    }
    
}else{
    $return["error"] = true;
    $return["msg"] =  "No file is submitted.";
}

header('Content-Type: application/json');
// tell browser that its a json data
echo json_encode($return);
//converting array to JSON string
?>