<?php

class AD_Model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	public function generateUniqueId()
    {
        $random_id_length = 10;

        //generate a random id encrypt it and store it in $rnd_id
        $rnd_id = uniqid(time(), 1);
        //to remove any slashes that might have come
        $rnd_id = strip_tags(stripslashes($rnd_id));
        //Removing any . or / and reversing the string
        $rnd_id = str_replace(".", "", $rnd_id);
        $rnd_id = strrev(str_replace("/", "", $rnd_id));
        //finally I take the first 10 characters from the $rnd_id
        $rnd_id = substr($rnd_id, 0, $random_id_length);

        return $rnd_id;
    }
}