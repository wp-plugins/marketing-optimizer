<?php

class mo_api_roles extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'roles');
        } else {
            $this->set_uri(self::APIURI . 'roles/' . $id);
        }
    }
}