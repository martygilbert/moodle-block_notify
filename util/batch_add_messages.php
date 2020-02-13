<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Block notify
 *
 * @package    block_notify
 * @copyright  Marty Gilbert <martygilbert@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', true);
require_once('../../../config.php');

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

$numadded = 0;
if (($handle = fopen("/tmp/messages.csv", "r")) !== false) {

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $notify = new stdClass();

        $notify->username   = $data[0];
        $notify->courseid   = $data[1];
        $notify->title      = $data[2];
        $notify->start      = $data[3];
        $notify->end        = $data[4];
        $notify->message    = $data[5];

        if (strlen($notify->title) > 50) {
            echo "Title too long. Skipping\n";
            continue;
        }

        if ($notify->username == "#N/A") {
            echo('No record for user with username: '.$notify->username."\n");
            continue;
        }

        $user  = $DB->get_record('user', array('username' => $notify->username));
        if (!$user) {
            echo('No record for user with username: '.$notify->username."\n");
            continue;
        }

        $notify->mdluserid = $user->id;

        $result = $DB->insert_record('block_notify', $notify, true, true);
        if (!$result) {
            echo('Zero returned for id inserting '.$notify->username."\n");
            continue;
        }
        $numadded++;
    }

    fclose($handle);

} else {
    echo ("Error opening file\n");
}

echo "Added $numadded messages\n";
