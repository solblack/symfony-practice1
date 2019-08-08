<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Pais;
use AppBundle\Entity\Provincia;


class BuscadorController extends Controller
{
    /**
     * @Route("/buscador", name="buscador")
     */
    public function index(Request $request)
    {  
        $busqueda = $request->get('search');
        $tipo = $request->get('tipo');
        
        switch($tipo){
            case 'pais':
            $paises = $this->buscarPais($busqueda); //retorna $paises
            return $this->render('paises.html.twig', ['paises' => $paises, 'abm' => false ]);
            break;

            case 'provincia':
            $provincias = $this->buscarProvincia($busqueda); //retorna $provincias
            return $this->render('provincias.html.twig', ['provincias' => $provincias, 'abm' => false ]);
            break;
        
            default:
            $this->addFlash('error', "Debe seleccionar si busca un país o una provincia"); 
            return $this->redirectToRoute("homepage");
            break; 
        }

        

    }

       /**
     */
    private function buscarPais($busqueda)
    {
        $repository = $this->getDoctrine()->getRepository(Pais::class);        

        $query = $repository->createQueryBuilder('paises')
                            ->where('paises.descripcion LIKE :busqueda')
                            ->setParameter('busqueda', '%' . $busqueda . '%')
                            ->orderBy('paises.descripcion', 'ASC')
                            ->getQuery();
    
        $paises = $query->getResult();

        if (empty($paises)) {
            $this->addFlash('success', "No se encontraron resultados con el término $busqueda"); 
        }

        return $paises;
    }

       /**
     */
    private function buscarProvincia($busqueda)
    {
        $repository = $this->getDoctrine()->getRepository(Provincia::class);        

        $query = $repository->createQueryBuilder('provincias')
                            ->where('provincias.descripcion LIKE :busqueda')
                            ->setParameter('busqueda', '%' . $busqueda . '%')
                            ->orderBy('provincias.descripcion', 'ASC')
                            ->getQuery();
    
        $provincias = $query->getResult();

      
        if (empty($provincias)) {
            $this->addFlash('success', "No se encontraron resultados con el término $busqueda"); 
        }

        return $provincias;
    }
    
}
