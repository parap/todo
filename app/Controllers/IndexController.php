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
        echo 'fetch';
    }
    
    public function remove()
    {
        echo 'remove';
    }
}
