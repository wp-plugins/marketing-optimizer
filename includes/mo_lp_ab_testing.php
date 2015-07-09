<?php

function mo_lp_ab_key_to_letter($key) {
	$alphabet = array (
			'A',
			'B',
			'C',
			'D',
			'E',
			'F',
			'G',
			'H',
			'I',
			'J',
			'K',
			'L',
			'M',
			'N',
			'O',
			'P',
			'Q',
			'R',
			'S',
			'T',
			'U',
			'V',
			'W',
			'X',
			'Y',
			'Z' 
	);
	
	if (isset ( $alphabet [$key] ))
		return $alphabet [$key];
}

function mo_lp_track_admin_user() {
    if (current_user_can('manage_options')) {
        if (get_option('mo_lp_track_admin') == 'true') {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}
