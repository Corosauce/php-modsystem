<?php

  $url = $_GET["url"];
  $server_root = "<REMOVED>";
  
  if (strlen($url) < 1) {
      die();
  }
  
  //Delete old zip cache
  $filename="mctempjar/net/minecraft/client/Minecraft.class";
  $output="";
  if (file_exists($filename)) { 
    unlink("mctempjar/net/minecraft/client/Minecraft.class");
  }
  
  //Download file
  dl_file($url,"/","minecrafttemp2.jar");
  
  
  //Extract file
  
  //servers that support ZipArchive class
  if (class_exists("ZipArchive")) {
      extractZip();
  } else {
      //This code is crafted for a specific dreamhost domain
      shell_exec("unzip -o " . $server_root . "mods/modsystem/minecrafttemp2.jar net/minecraft/client/Minecraft.class -d mctempjar");
  }
  
  //Open class file for reading
  $filename="mctempjar/net/minecraft/client/Minecraft.class";
  $output="";
  if (!file_exists($filename)) { die(); }
  $file = fopen($filename, "r");
  while(!feof($file)) {

    //read file line by line into variable until we find the data we need
    $output = fgets($file, 4096);
    $strlook = "Minecraft Minecraft ";
    $strlen = strlen($strlook);
    if (strpos($output,$strlook) > 0) {
        $start = strpos($output,$strlook);
        //character ascii code '1' scan
        $end = strpos($output,"", $start+$strlen);
        $output = substr($output,$start + $strlen, $end-$start-$strlen);
        break;
    }
     
  }
  fclose ($file);
  echo "Extracted Minecraft Version: " . $output; 
      
function extractZip() {
    try {
        $zip = new ZipArchive;
        $res = $zip->open('minecrafttemp.jar');
        if ($res === TRUE) {
            //echo 'ok';
            $zip->extractTo('mctempjar', 'net/minecraft/client/Minecraft.class');
            $zip->close();
        } else {
            //echo 'failed, code:' . $res;
        }
    } catch (Exception $ex) { return false; }
    return true;
}
  
function dl_file($file, $local_path, $newfilename)
{
    $err_msg = '';
    $out = fopen($newfilename, 'wb');
    if ($out == FALSE){
      print "File not opened<br>";
      exit;
    }
   
    $ch = curl_init();
           
    curl_setopt($ch, CURLOPT_FILE, $out);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, $file);
               
    curl_exec($ch);
    //echo "<br>Error is : ".curl_error ( $ch);
   
    curl_close($ch);
}
?>
