<?php

namespace Controllers;

use Classes\ItemRepo;
use Symfony\Component\HttpFoundation\Request;
use Classes\ItemType;
use Classes\UriParser;

/**
 * Description of IndexController
 *
 * @author user
 */
class IndexController
{

    protected $repo;
    protected $post;
    protected $queryHelper;

    public function __construct()
    {
        $this->pr   = new UriParser();
        $this->repo = new ItemRepo();

        // json_decode($request->getContent()) can be used alternately
        $this->post = json_decode(file_get_contents('php://input'), true);
    }

    public function fetch(Request $request)
    {
        $date    = (new \DateTime($this->pr->get($request, 'day') . ' day'))->format('Y-m-d');
        $results = $this->repo->fetch($date);
        $json    = json_encode($results);

        return $json;
    }

    public function fetchArchived(Request $request)
    {
        $day     = $this->getUriParameter($request, 'day');
        $date    = (new \DateTime($day . ' day'))->format('Y-m-d');
        $results = $this->repo->fetchArchived($date);
        $json    = json_encode($results);

        return $json;
    }

    public function archive(Request $request)
    {
        $id = $this->post['id'];
        $this->repo->archive($id);
    }

    public function unarchive(Request $request)
    {
        $id = $this->post['id'];
        $this->repo->unarchive($id);
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
        $id   = $this->post['id'];
        $this->repo->update($id, $name);
    }

    public function complete(Request $request)
    {
        $id   = $this->post['id'];
        $type = $this->post['type'];
        $day  = $this->post['day'];
        $date = (new \DateTime($day . ' day'))->format('Y-m-d');

        $method = $this->post['done'] ? 'complete' : 'uncomplete';
        $this->repo->$method($id, $type, $date);

        $results = $this->repo->findDelay($id);

        return isset($results[0]['delay']) ? $results[0]['delay'] : '';
    }

    public function render()
    {
        return file_get_contents('index.html');
    }

}
