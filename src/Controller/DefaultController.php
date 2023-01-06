<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="default_index" )
 */
class DefaultController extends AbstractController{

    /**
     * @Route("/", name="default_index" )
     */
    public function index(){
        return new JsonResponse([
            "data" =>"datass",
            "hello"=> date('Y-m-d')
        ]);
    }
}