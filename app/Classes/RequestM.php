<?php

namespace Classes;

use Symfony\Component\HttpFoundation\Request;

class RequestM extends Request
{
    public function getM($param)
    {
        $elements = $this->getElements();
        
        return array_key_exists($param, $elements) ? $elements[$param] : null;
    }
    
    private function getElements()
    {
        if (!$this->server->get('REQUEST_URI')) {
            return [];
        }

        $params = explode('fetch?', $this->server->get('REQUEST_URI'));
        $params1 = explode("&", $params[1]);
        
        $result = [];
        
        foreach($params1 as $value) {
            $params2 = explode("=", $value);
            $result[$params2[0]] = $params2[1];
        }
        
        return $result;
    }
}
