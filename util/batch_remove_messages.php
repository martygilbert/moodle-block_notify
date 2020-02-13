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
    This script will remove all messages for a list of users for the notify block
    CSV will need to be:
    0 - email
*/
global $CFG, $USER;

$numremoved = 0;
if (($handle = fopen("/tmp/toRemove.csv", "r")) !== false) {

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $notify = new stdClass();

        $notify->email   = $data[0];

        $user  = $DB->get_record('user', array('email' => $notify->email));
        if (!$user) {
            echo('No record for user with email: '.$notify->email."\n");
            continue;
        }

        $result = $DB->delete_records('block_notify', array('mdluserid' => $user->id));
        if (!$result) {
            echo('False returned for delete messages for '.$notify->email."\n");
            continue;
        }
        $numremoved++;
    }

    fclose($handle);

} else {
    echo ("Error opening file\n");
}

echo "Removed messages for $numremoved users\n";
