<?php

namespace Controllers;

use Classes\ItemRepo;
use Classes\RequestM;
use Classes\ItemType;

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

    public function fetch(RequestM $request)
    {
        $date    = (new \DateTime((int)$request->getM('day') . ' day'))->format('Y-m-d');
        $email   = $request->getM('email');
        $results = $this->repo->fetch($date, $email);
        $json    = json_encode($results);

        return $json;
    }

    public function fetchArchived(RequestM $request)
    {
        $date    = (new \DateTime((int)$request->getM('day') . ' day'))->format('Y-m-d');
        $email   = $request->getM('email');
        $results = $this->repo->fetchArchived($date, $email);
        $json    = json_encode($results);

        return $json;
    }

    public function archive(RequestM $request)
    {
        $id = $request->postM['id'];
        $this->repo->archive($id);
    }

    public function remove(RequestM $request)
    {
        $id = $request->postM['id'];
        $this->repo->remove($id);
    }

    public function unarchive(RequestM $request)
    {
        $id = $request->postM['id'];
        $this->repo->unarchive($id);
    }

    public function create(RequestM $request)
    {
        $name = $request->postM['name'];
        $type = $request->postM['type'];
        $email = $request->postM['email'];
        $params = $request->postM['params'];
        
        if (empty($name)) {
            return;
        }
        
        $parentId = 1;
        $this->repo->create($name, $email, $parentId, $type, $params);
    }

    public function update(RequestM $request)
    {
        $name    = $request->postM['name'];
        $id      = $request->postM['id'];
        $type    = $request->postM['type'];
        $numbers = $request->postM['numbers'];
        $this->repo->update($id, $name, $type, $numbers);
    }

    public function complete(RequestM $request)
    {
        $type = $request->postM['type'];
        $id   = $request->postM['id'];
        $day  = $request->postM['day'];
        $date = (new \DateTime($day . ' day'))->format('Y-m-d');

        $method = $request->postM['done'] ? 'complete' : 'uncomplete';
        $this->repo->$method($id, $type, $date);

        $results = $this->repo->findDelay($id);

        return isset($results[0]['delay']) ? $results[0]['delay'] : '';
    }
    
    public function setDate(RequestM $request)
    {
        $id   = $request->postM['id'];
        $date  = $request->postM['date'];
        $this->repo->setDate($id, $date);
        echo json_encode($this->repo->findTimeLeft($id));
    }

    public function render()
    {
        return file_get_contents('index.html');
    }
}
