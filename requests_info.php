<?php
##############################
$filename = "text.txt"; // файл, в который будет записываться информация
$clear_file_before_write = true; // очищать файл перед записью. true - да, false - нет
##############################

$posts_result = null;
$posts = count($_POST) - 1;
$post_keys = array_keys($_POST);
for($i = -1; $i ++< $posts;) {
    $posts_result = $post_keys[$i]." => ".$_POST[$post_keys[$i]]."\n".$posts_result;
}

$get_result = null;
$gets = count($_GET) - 1;
$get_keys = array_keys($_GET);
for($i = -1; $i ++< $gets;) {
    $gets_result = $get_keys[$i]." => ".$_GET[$get_keys[$i]]."\n".$gets_result;
}

$request_result = null;
$requests = count($_REQUEST) - 1;
$request_keys = array_keys($_REQUEST);
for($i = -1; $i ++< $requests;) {
    $requests_result = $request_keys[$i]." => ".$_REQUEST[$request_keys[$i]]."\n".$requests_result;
}

if(! $clear_file_before_write) {
    $file = fopen($filename, "a");

    fwrite($file, "######################################\n# ".date("H:i:s d.m.Y", time())." FROM ".$_SERVER['REMOTE_ADDR']."\n######################################\n################ POST ################\n$posts_result\n\n\n################ GET ################\n$gets_result\n\n\n################ REQUEST ################\n$requests_result\n\n\n\n\n");

    fclose($file);
} else {
    file_put_contents($filename, "######################################\n# ".date("H:i:s d.m.Y", time())." FROM ".$_SERVER['REMOTE_ADDR']."\n######################################\n################ POST ################\n$posts_result\n\n\n################ GET ################\n$gets_result\n\n\n################ REQUEST ################\n$requests_result\n\n\n\n\n");
}
?>
