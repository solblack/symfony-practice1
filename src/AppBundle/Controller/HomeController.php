<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {  
        return $this->render('home.html.twig', [ ]);
    }
    
}
