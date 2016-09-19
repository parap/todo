<?php

namespace Controllers;

use Classes\ItemRepo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of IndexController
 *
 * @author user
 */
class StatisticController 
{
    protected $repo;
    protected $post;
    
    public function __construct()
    {
        $this->repo = new ItemRepo();
        
        // json_decode($request->getContent()) can be used alternately
        $this->post = json_decode(file_get_contents('php://input'),true);
    }
    
    public function index(Request $request)
    {
        $daily = $this->repo->dailySingleStatistic();
        $total = $this->repo->fetchStatistic();
        $results = ['daily' => $daily, 'total' => $total];
        
        return json_encode($results);
    }
}
