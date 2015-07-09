<?php

class mo_api_auth extends mo_api
{

    private $username;

    private $password;

    public function __construct($username, $password)
    {
        parent::__construct();
        $this->set_username($username);
        $this->set_password($password);
        $this->set_uri(self::APIURI . 'login/' . $this->get_username() . '/' . $this->get_password());
        return $this;
    }

    public function get_password()
    {
        return $this->password;
    }

    public function set_password($password)
    {
        $this->password = $password;
        return $this;
    }

    public function get_username()
    {
        return $this->username;
    }

    public function set_username($username)
    {
        $this->username = $username;
        return $this;
    }
}