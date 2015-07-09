<?php

class mo_api_tests extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'tests');
        } else {
            $this->set_uri(self::APIURI . 'tests/' . $id);
        }
    }
}