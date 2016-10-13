<?php

namespace Classes;

use Symfony\Component\HttpFoundation\Request;

/**
 * @brief to parse URI
 */
class UriParser
{
    public function get(Request $request, $param)
    {
        $elements = $this->getElements($request);
        
        return array_key_exists($param, $elements) ? $elements[$param] : null;
    }
    
    private function getElements(Request $request)
    {
        if (!$request->server->get('REQUEST_URI')) {
            return [];
        }

        $params = explode('fetch?', $request->server->get('REQUEST_URI'));
        $params1 = explode("&", $params[1]);
        
        $result = [];
        
        foreach($params1 as $value) {
            $params2 = explode("=", $value);
            $result[$params2[0]] = $params2[1];
        }
        
        return $result;
    }
}
