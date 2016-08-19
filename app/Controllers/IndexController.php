<?php

namespace Controllers;

use Classes\ItemRepo;

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
    
    public function render()
    {
        echo 'render';
    }
    
    public function fetch()
    {
        $results = $this->repo->fetch();
        $json = json_encode($results);
        return $json;
    }
    
    public function remove()
    {
        echo 'remove';
    }
}
