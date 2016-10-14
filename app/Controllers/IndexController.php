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
        $results = $this->repo->fetchArchived($date);
        $json    = json_encode($results);

        return $json;
    }

    public function archive(RequestM $request)
    {
        $id = $request->postM['id'];
        $this->repo->archive($id);
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

        if (!empty($name)) {
            $this->repo->create($name, 1, 1, $type ? ItemType::Daily : ItemType::Normal);
        }
    }

    public function update(RequestM $request)
    {
        $name = $request->postM['name'];
        $id   = $request->postM['id'];
        $this->repo->update($id, $name);
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

    public function render()
    {
        return file_get_contents('index.html');
    }

}
