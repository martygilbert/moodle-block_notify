<?php

define('CLI_SCRIPT', true);
require_once('../../../config.php');

//require_once($CFG->dirroot.'/lib/accesslib.php');

/*
    This script will add messages to users for the notify block   
    CSV will need to be:
    0 - username
    1 - mdlcourseid
    2 - title
    3 - start
    4 - end
    5 - message
*/
global $CFG, $USER;

if ($argc != 2){
    echo "Requires filename.\nExiting.\n";
    exit;
}

$file = $argv[1];

if (!file_exists($file)){
    echo "File does not exist.\nExiting.\n";
    exit;
}


$numAdded = 0;
if(($handle = fopen($file, "r")) !== FALSE){

    while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
        $notify = new stdClass();

        $notify->username   = $data[0];
        $notify->courseid   = $data[1];
        $notify->title      = $data[2];
        $notify->start      = $data[3];
        $notify->end        = $data[4];
        $notify->message    = $data[5];

        if(strlen($notify->title) > 50){
            echo "Title too long. Skipping\n";
            continue;
        }

        if($notify->username == "#N/A"){
            echo('No record for user with username: '.$notify->username."\n");
            continue;
        }

        $user  = $DB->get_record('user', array('username'=>$notify->username));
        if(!$user){
            echo('No record for user with username: '.$notify->username."\n");
            continue;
        }

        $notify->mdluserid = $user->id;

        $result = $DB->insert_record('block_notify', $notify, true, true); //returnid, bulk
        if(!$result){
            echo('Zero returned for id inserting '.$notify->username."\n");
            continue;
        }
		$numAdded++;
    }

    fclose($handle);

} else {
    echo ("Error opening file\n");
}

echo "Added $numAdded messages\n";
