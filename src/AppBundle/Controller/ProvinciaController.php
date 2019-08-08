<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Pais;
use AppBundle\Entity\Provincia;


class ProvinciaController extends Controller
{
    /**
     * @Route("/provincias", name="provincias")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $provincias = $em->getRepository('AppBundle:Provincia')->findBy(array('activo'=>true), array('descripcion' => 'ASC'));

        return $this->render('provincias.html.twig', ['provincias' => $provincias, 'abm' => false ]);
    }

    /**
     * @Route("/provincias/adminlist", name="adminListProvincias")
     */
    public function adminListProvincias(Request $request)
    {
        if ($request->get('action')) {
            $action = $request->get('action');
            $idProvincia = $request->get('idProvincia');
            
            switch ($action) {
                case 'borrar':
                    $this->borrar($idProvincia);
                    break;
                case 'desactivar':
                    $this->desactivar($idProvincia);
                    break;
                case 'activar':
                    $this->activar($idProvincia);
                    break;
                
                default:
                    $this->addFlash('error', "La acción seleccionada no es válida"); 
                    break; 
            }
            
        }

        $em = $this->getDoctrine()->getManager();
        
        $provincias = $em->getRepository(Provincia::class)->findBy(array(), array('descripcion' => 'ASC'));

        return $this->render('provincias.html.twig', ['provincias' => $provincias, 'abm' => true ]);
    }

    /**
     * @Route("/provincias/{id}", name="detalleProvincia", requirements={"id"="\d+"})
     */
    public function detalleProvincia($id, Request $request)
    {
        if ($request->get('action')) {
            $action = $request->get('action');
                        
            switch ($action) {
                case 'borrar':
                    $this->borrar($id);
                    return $this->redirectToRoute('provincias');
                    break;
                case 'desactivar':
                    $this->desactivar($id);
                    break;
                case 'activar':
                    $this->activar($id);
                    break;
                
                default:
                    $this->addFlash('error', "La acción seleccionada no es válida"); 
                    break; 
            }
            
        }

        $em = $this->getDoctrine()->getManager();

        $provincia = $em->getRepository(Provincia::class)->findOneById($id);

        return $this->render('detalleprovincia.html.twig', ['provincia' => $provincia ]);
    }

     /**
     * @Route("/provincias/editar/{id}", name="editarProvincia", requirements={"id"="\d+"})
     */
    public function editarProvincia($id, Request $request)
    {
        if ($request->get('action')) {
            $action = $request->get('action');
                        
            switch ($action) {
                case 'guardar':
                    $this->guardar($request); 
                    $url= $this->generateUrl('detalleProvincia', [ 'id' => $id,]);
                    return $this->redirect($url);
                    break;
               
                default:
                    $this->addFlash('error', "La acción seleccionada no es válida"); 
                    break; 
            }
            
        }

        $em = $this->getDoctrine()->getManager();

        $provincia = $em->getRepository(Provincia::class)->findOneById($id);
     
        $paises = $em->getRepository(Pais::class)->findBy(array('activo'=>true), array('descripcion' => 'ASC'));

        return $this->render('editarprovincia.html.twig', ['provincia' => $provincia, 'paises' => $paises  ]);
    }

    /**
     * @Route("/provincias/nueva", name="nuevaProvincia")
     */
    public function nuevaProvincia(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $paises = $em->getRepository('AppBundle:Pais')->findBy(array('activo'=>true), array('descripcion' => 'ASC'));

        return $this->render('nuevaprovincia.html.twig', ['paises' => $paises ]);

    }
     
     /**
     * @Route("/provincias/crear", name="crearProvincias")
     */
    public function crear(Request $request)
    {
        $provinciaDescripcion = strtoupper($request->get('provincia'));
        $provinciaAbrev = strtoupper($request->get('abrev'));
        $provinciaActivo = $request->get('activo');
        $provinciaPaisId = $request->get('paisId');

        $em = $this->getDoctrine()->getManager();

        $provinciaPais = $em->getRepository(Pais::class)->findOneById($provinciaPaisId);
    
        $provincia = new Provincia();

        $provincia->setDescripcion($provinciaDescripcion);
        $em->persist($provincia);

        $provincia->setAbrev($provinciaAbrev);
        $em->persist($provincia);

        $provincia->setActivo($provinciaActivo);
        $em->persist($provincia);

        $provincia->setPais($provinciaPais);
        $em->persist($provincia);

        $em->flush($provincia);

        $nombreProvincia = $provincia->getDescripcion();
        
        $this->addFlash('success', "La provincia $nombreProvincia ha sido creada con éxito");

        return $this->redirectToRoute('provincias');
    }

    /**
     */
    private function borrar($id)
    {
        $em = $this->getDoctrine()->getManager();

        $provincia = $em->getRepository(Provincia::class)->findOneById($id);
        $em->remove($provincia);
        $em->flush();

        $nombreProvincia = $provincia->getDescripcion();

        $this->addFlash('success', "La provincia $nombreProvincia ha sido borrada");
        
        return $this->redirectToRoute('provincias');
    }
  

    /**
     */
    private function desactivar($id)
    {
        $em = $this->getDoctrine()->getManager();
        $provincia = $em->getRepository(Provincia::class)->findOneById($id);
    
        $provincia->setActivo("0");
        $em->flush($provincia);
        
        $nombreProvincia = $provincia->getDescripcion();

        $this->addFlash('success', "La provincia $nombreProvincia ha sido desactivada");

        return $provincia;
    }

    /**
     */
    private function activar($id)
    {
        $em = $this->getDoctrine()->getManager();
        $provincia = $em->getRepository(Provincia::class)->findOneById($id);
    
        $provincia->setActivo("1");
        $em->flush($provincia);

        $nombreProvincia = $provincia->getDescripcion();

        $this->addFlash('success', "La provincia $nombreProvincia ha sido activada");
        
        return $provincia;
    }

    private function guardar(Request $request)
    {
        $provinciaId = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        $provincia = $em->getRepository(Provincia::class)->findOneById($provinciaId);

        if ($request->get('provincia')) {
            $provinciaDescripcion = strtoupper($request->get('provincia'));
            $provincia->setDescripcion($provinciaDescripcion);
        }

        if ($request->get('abrev')) {
            $provinciaAbrev = strtoupper($request->get('abrev'));
            $provincias->setAbrev($provinciaAbrev);
        }
        
        if ($request->get('activo')) {
            $provinciaActivo = $request->get('activo');
            $provincia->setActivo($provinciaActivo);
        }
        
        $em->flush($provincia);

        $provinciaGuardada = $em->getRepository(Provincia::class)->findOneById($provinciaId);
        
        $nombreProvincia = $provinciaGuardada->getDescripcion();

        $this->addFlash('success', "La provincia $nombreProvincia ha sido guardada");   

          
        return $provinciaGuardada;
        
    
    }

}
