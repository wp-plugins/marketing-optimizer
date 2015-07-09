<?php

class mo_api_criteria extends mo_api
{

    public function __construct($id = false)
    {
        parent::__construct($filter_id = false, $criteria_id = false);
        if (! $filter_id) {
            throw new InvalidArgumentException('Invalid argument passed for $filter_id, $filter_id must be an int and not false. $filter_id:' . $filter_id ? $filter_id : 'false');
            $this->set_uri(self::APIURI . 'filters');
        } elseif (! $criteria_id) {
            $this->set_uri(self::APIURI . 'filters/' . $id);
        } else {
            $this->set_uri(self::APIURI . 'filters/' . $id . '/criteria/' . $criteria_id);
        }
    }
}