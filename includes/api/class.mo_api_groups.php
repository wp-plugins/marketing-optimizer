<?php

class mo_api_groups extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'groups');
        } else {
            $this->set_uri(self::APIURI . 'groups/' . $id);
        }
    }
}