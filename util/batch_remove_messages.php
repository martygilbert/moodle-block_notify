<?php

define('CLI_SCRIPT', true);
require_once('../../../config.php');

//require_once($CFG->dirroot.'/lib/accesslib.php');

/*
    This script will remove all messages for a list of users for the notify block   
    CSV will need to be:
    0 - email
*/
global $CFG, $USER;

$numRemoved = 0;
if(($handle = fopen("/tmp/toRemove.csv", "r")) !== FALSE){

    while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
        $notify = new stdClass();

        $notify->email   = $data[0];

        $user  = $DB->get_record('user', array('email'=>$notify->email));
        if(!$user){
            echo('No record for user with email: '.$notify->email."\n");
            continue;
        }

        $result = $DB->delete_records('block_notify', array('mdluserid' => $user->id));
        if(!$result){
            echo('False returned for delete messages for '.$notify->email."\n");
            continue;
        }
		$numRemoved++;
    }

    fclose($handle);

} else {
    echo ("Error opening file\n");
}

echo "Removed messages for $numRemoved users\n";
