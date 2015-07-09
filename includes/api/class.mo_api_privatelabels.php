<?php

class mo_api_privatelabels extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'privatelabels');
        } else {
            $this->set_uri(self::APIURI . 'privatelabels/' . $id);
        }
    }
}