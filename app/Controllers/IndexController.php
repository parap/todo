<?php

namespace Controllers;

use Classes\ItemRepo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of IndexController
 *
 * @author user
 */
class IndexController 
{
    protected $repo;
    public function __construct()
    {
        $this->repo = new ItemRepo();
    }
    
    public function render(Request $request)
    {
        echo 'render';
    }
    
    public function fetch(Request $request)
    {
        $results = $this->repo->fetch();
        $json = json_encode($results);
        
        return $json;
    }
    
    public function remove(Request $request)
    {
        echo 'remove';
    }
    
    public function create(Request $request)
    {
        
    }
    
    public function update(Request $request)
    {
        
    }
    
    public function complete(Request $request)
    {
        
    }
    
    public function uncomplete(Request $request)
    {
        
    }
}
