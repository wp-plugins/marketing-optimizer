<?php
/*  Copyright 2010 Juergen Schulze, 1manfactory.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action( 'admin_notices', 'mo_check_create_log_folder' );

// create log folder
// return true if folder has been created or exists else false
function mo_createLogFolder() {
	$error=false;
	if (!@is_dir (MO_LOGPATH)) {
		if (!@mkdir (MO_LOGPATH, 0777)) {
			$mo_on_off=0;
			update_option('mo_on_off', 0);
			$error=true;
		}
	}

	# check if log folder is writeable
	if (!@is_writable(MO_LOGPATH) ) {

		# trying to set permissions
		if (!@chmod(MO_LOGPATH, 0777)) {
			$mo_on_off=0;
			update_option('mo_on_off', 0);
			$error=true;
		}
	} else {
		# create empty index.html file to hide logs from browsing
		$emptyFile=MO_LOGPATH.'index.html';
		$fileWrite = fopen($emptyFile, 'a');
		fclose($fileWrite);
	}
	if ($error) return false; else return true;
}

# delete log folder
function mo_deleteLogFolder() {
	# delete log folder and logs
	if (@is_dir(MO_LOGPATH)) {
		mo_deltree(mo_LOGPATH);
	}
}

// try to create log folder and print success or error on admin panel
function mo_check_create_log_folder() {
	# check for folder
	if (mo_check_folder_error()) {
		if (!mo_createLogFolder()) {
			print '<div id="message" class="error">'.__("Marketing Optimizer Error: Can't write to log folder ", EMU2_I18N_DOMAIN).MO_LOGPATH.__(" Permissions 777 needed.", EMU2_I18N_DOMAIN).'</div>';
		} else {
			print '<div id="message" class="updated">'.__("Marketing Optimizer: Log folder created: ", EMU2_I18N_DOMAIN).MO_LOGPATH.'</div>';
		}
	}
}

function mo_check_folder_error() {
	$error=false;
	# check if log folder is writeable
	if (!@is_writable(MO_LOGPATH) ) {
		# trying to set permissions
		if (!@chmod(MO_LOGPATH, 0777)) {
			// can't write to or create log folder
			// maybe you should switch you lugin to off now
			// $mo_on_off=false;
			$error=true;
		}
	}
	return $error;
}

# deletes all files and folders and subfolders in given folder
function mo_deltree($f) {
	if (@is_dir($f)) {
		foreach(glob($f.'/*') as $sf) {
			if (@is_dir($sf) && !is_link($sf)) {
				@mo_deltree($sf);
			} else {
				@unlink($sf);
			}
		}
	}
	if (@is_dir($f)) rmdir($f);
	return true;
}


# write a message to the logfile
function mo_writelog() {
	$numargs = func_num_args();
	$arg_list = func_get_args();
	if ($numargs >2) $linenumber=func_get_arg(2); else $linenumber="";
	if ($numargs >1) $functionname=func_get_arg(1); else $functionname="";
	if ($numargs >=1) $string=func_get_arg(0);
	if (!isset($string) or $string=="") return;

	$logFile=MO_LOGPATH.'/ops-'.date("Y-m").".log";
	$timeStamp = date("d/M/Y:H:i:s O");

	$fileWrite = fopen($logFile, 'a');

	//flock($fileWrite, LOCK_SH);
	if (mo_debug()) {
		$logline="[$timeStamp] ".html_entity_decode($string)." $functionname $linenumber\r\n";	# for debug purposes
	} else {
		$logline="[$timeStamp] ".html_entity_decode($string)."\r\n";
	}
	fwrite($fileWrite, utf8_encode($logline));
	//flock($fileWrite, LOCK_UN);
	fclose($fileWrite);
}

?>