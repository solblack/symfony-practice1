<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ExampleController extends Controller
{
    /**
     * @Route("/example", name="example")
     */
    public function indexAction(Request $request)
    {
        $a = 'Ruta';
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            // 'base_dir' => $a, //realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

  
}
