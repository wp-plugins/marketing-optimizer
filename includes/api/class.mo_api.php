<?php

class mo_api
{

    const APIURI = 'https://app.marketingoptimizer.com/api/v1/';

    private $cookie_name = 'mo_api_live';

    private $error = false;

    private $is_new_session = false;

    private $request;

    private $request_type = 'GET';

    private $response;

    private $uri = self::APIURI;

    public function __construct()
    {
        return $this;
    }

    public function get_cookie_name()
    {
        return $this->cookie_name;
    }

    public function set_cookie_name($cookie_name)
    {
        $this->cookie_name = $cookie_name;
        return $this;
    }

    public function get_error()
    {
        return $this->error;
    }

    public function set_error($error)
    {
        $this->error = $error;
        return $this;
    }

    public function get_is_new_session()
    {
        return $this->is_new_session;
    }

    public function set_is_new_session($is_new_session)
    {
        $this->is_new_session = $is_new_session;
        return $this;
    }

    public function get_request()
    {
        return $this->request;
    }

    public function set_request($request)
    {
        $this->request = $request;
        return $this;
    }

    public function get_request_type()
    {
        return $this->request_type;
    }

    public function set_request_type($request_type)
    {
        $this->request_type = $request_type;
        return $this;
    }

    public function get_response()
    {
        return $this->response;
    }

    public function set_response($response)
    {
        $this->response = $response;
        return $this;
    }

    public function get_uri()
    {
        return $this->uri;
    }

    public function set_uri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function execute()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->get_uri());
        curl_setopt($ch, CURLOPT_HEADER, 0);
        switch ($this->get_request_type()) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->get_request());
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->get_request_type());
                break;
            default:
                break;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        if ($this->get_is_new_session()) {
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        } else {
            curl_setopt($ch, CURLOPT_COOKIE, 'sessioncrm=' . $_COOKIE[$this->get_cookie_name()]);
        }
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            $this->set_error($error);
        }
        $this->set_response($response);
        return $this;
    }
}
