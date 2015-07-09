<?php

class mo_api_accounts extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'accounts');
        } else {
            $this->set_uri(self::APIURI . 'accounts/' . $id);
        }
    }
}