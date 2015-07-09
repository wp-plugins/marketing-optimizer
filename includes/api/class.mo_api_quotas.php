<?php

class mo_api_quotas extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct();
        if (! $id) {
            $this->set_uri(self::APIURI . 'quotas');
        } else {
            $this->set_uri(self::APIURI . 'quotas/' . $id);
        }
    }
}