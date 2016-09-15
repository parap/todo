<?php

namespace Controllers;

use Classes\ItemRepo;
use Symfony\Component\HttpFoundation\Request;
use Classes\ItemType;

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
        
        // json_decode($request->getContent()) can be used alternately
        $this->post = json_decode(file_get_contents('php://input'),true);
    }
    
    public function fetch(Request $request)
    {
        // FIXME - remove ugly REQUEST_URI thing
        $day = (int)explode('=', $_SERVER['REQUEST_URI'])[1];
        $date = (new \DateTime($day.' day'))->format('Y-m-d');
        $results = $this->repo->fetch($date);
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
        $type = $this->post['type'];

        if (!empty($name)) {
            $this->repo->create($name, 1, 1, $type ? ItemType::Daily : ItemType::Normal);
        }
    }

    public function update(Request $request)
    {
        $name = $this->post['name'];
        $id = $this->post['id'];
        $this->repo->update($id, $name);
    }

    public function complete(Request $request)
    {
        $id = $this->post['id'];
        $type = $this->post['type'];
        
        $this->post['done'] ? $this->repo->complete($id, $type) : $this->repo->uncomplete($id, $type);
    }

}
