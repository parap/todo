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
    protected $post;
    
    public function __construct()
    {
        $this->repo = new ItemRepo();
        $this->post = json_decode(file_get_contents('php://input'),true);
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
        $id = $this->post['id'];
        $this->repo->remove($id);
    }
    
    public function create(Request $request)
    {
        $name = $this->post['name'];

        if (!empty($name)) {
            $this->repo->create($name, 1, 1, 0);
        }
    }

    public function update(Request $request)
    {

    }

    public function complete(Request $request)
    {
        $done = $this->post['done'];
        $id = $this->post['id'];
        $this->post['done'] ? $this->repo->complete($id) : $this->repo->uncomplete($id);
    }

}
