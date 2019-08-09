<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Pais;


class PaisController extends Controller
{
    /**
     * @Route("/paises", name="paises")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $paises = $em->getRepository('AppBundle:Pais')->findBy(array('activo'=>true), array('descripcion' => 'ASC'));

        return $this->render('paises.html.twig', ['paises' => $paises, 'abm' => false ]);
    }

    /**
     * @Route("/paises/adminlist", name="adminListPaises")
     */
    public function adminListPaises(Request $request)
    {
        if ($request->get('action')) {
            $action = $request->get('action');
            $idPais = $request->get('idPais');
            
            switch ($action) {
                case 'borrar':
                    $this->borrar($idPais);
                    break;
                case 'desactivar':
                    $this->desactivar($idPais);
                    break;
                case 'activar':
                    $this->activar($idPais);
                    break;
                
                default:
                    $this->addFlash('error', "La acción seleccionada no es válida"); 
                    break; 
            }
            
        }

        $em = $this->getDoctrine()->getManager();
        
        $paises = $em->getRepository(Pais::class)->findBy(array(), array('descripcion' => 'ASC'));

        return $this->render('paises.html.twig', ['paises' => $paises, 'abm' => true ]);
    }

    /**
     * @Route("/paises/{id}", name="detallePais", requirements={"id"="\d+"})
     */
    public function detallePais($id, Request $request)
    {
        if ($request->get('action')) {
            $action = $request->get('action');
                        
            switch ($action) {
                case 'borrar':
                    $this->borrar($id);
                    return $this->redirectToRoute('paises');
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

        $pais = $em->getRepository(Pais::class)->findOneById($id);

        return $this->render('detallepais.html.twig', ['pais' => $pais ]);
    }

     /**
     * @Route("/paises/editar/{id}", name="editarPais", requirements={"id"="\d+"})
     */
    public function editarPais($id, Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();

        $pais = $em->getRepository(Pais::class)->findOneById($id);
      

        $form = $this->createFormBuilder($pais)
                ->add('descripcion', TextType::class, ['label' => 'Nombre:',
                ])

                ->add('abrev', TextType::class, ['label' => 'Abreviación:',
                ])

                ->add('activo', ChoiceType::class, ['label' => 'Estado:',
                'choices'  => ['ACTIVO' => '1', 'NO ACTIVO' => '0']])

                ->add('save', SubmitType::class, ['label' => 'Guardar',
                'attr' => ['class' => 'btn-style1 btn-center']])

                ->getForm();    

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pais = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pais);
            $entityManager->flush();

            $nombrePais = $pais->getDescripcion();

            $this->addFlash('success', "El país $nombrePais ha sido editado");

            $url= $this->generateUrl('detallePais', [ 'id' => $id,]);
            return $this->redirect($url);

        }

        return $this->render('editarpais.html.twig', ['pais' => $pais, 'form' => $form->createView() ]);
      

    }

    /**
     * @Route("/paises/nuevo", name="nuevoPais")
     */
    public function nuevoPais(Request $request)
    {
        $pais = new Pais();

        $form = $this->createFormBuilder($pais)
                ->add('descripcion', TextType::class, ['label' => 'Nombre:',
                ])

                ->add('abrev', TextType::class, ['label' => 'Abreviación:',
                ])

                ->add('activo', ChoiceType::class, ['label' => 'Estado:',
                'choices'  => ['ACTIVO' => '1', 'NO ACTIVO' => '0']])

                ->add('save', SubmitType::class, ['label' => 'Guardar',
                'attr' => ['class' => 'btn-style1 btn-center']])

                ->getForm();    

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pais = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pais);
            $entityManager->flush();

            $nombrePais = $pais->getDescripcion();

            $this->addFlash('success', "El país $nombrePais ha sido creado");

            return $this->redirectToRoute('paises');

        }

        return $this->render('nuevopais.html.twig', ['form' => $form->createView() ]);

    }
     
     /**
     * @Route("/paises/crear", name="crearPaises")
     */
    public function crear(Request $request)
    {

        $paisDescripcion = strtoupper($request->get('pais'));
        $paisAbrev = strtoupper($request->get('abrev'));
        $paisActivo = $request->get('activo');

        $em = $this->getDoctrine()->getManager();
    
        $pais = new Pais();

        $pais->setDescripcion($paisDescripcion);
        $em->persist($pais);

        $pais->setAbrev($paisAbrev);
        $em->persist($pais);

        $pais->setActivo($paisActivo);
        $em->persist($pais);

        $em->flush($pais);

        $nombrePais = $pais->getDescripcion();
        
        $this->addFlash('success', "El país $nombrePais ha sido creado con éxito");

        return $this->redirectToRoute('paises');
    }

    /**
     */
    private function borrar($id)
    {
        $em = $this->getDoctrine()->getManager();

        $pais = $em->getRepository(Pais::class)->findOneById($id);
        $em->remove($pais);
        $em->flush();

        $nombrePais = $pais->getDescripcion();

        $this->addFlash('success', "El país $nombrePais ha sido borrado");
        
        return $this->redirectToRoute('paises');
    }
  

    /**
     */
    private function desactivar($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pais = $em->getRepository(Pais::class)->findOneById($id);
    
        $pais->setActivo("0");
        $em->flush($pais);
        
        $nombrePais = $pais->getDescripcion();

        $this->addFlash('success', "El país $nombrePais ha sido desactivado");

        return $pais;
    }

    /**
     */
    private function activar($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pais = $em->getRepository(Pais::class)->findOneById($id);
    
        $pais->setActivo("1");
        $em->flush($pais);

        $nombrePais = $pais->getDescripcion();

        $this->addFlash('success', "El país $nombrePais ha sido activado");
        
        return $pais;
    }

    private function guardar(Request $request)
    {
        $paisId = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        $pais = $em->getRepository(Pais::class)->findOneById($paisId);

        if ($request->get('pais')) {
            $paisDescripcion = strtoupper($request->get('pais'));
            $pais->setDescripcion($paisDescripcion);
        }

        if ($request->get('abrev')) {
            $paisAbrev = strtoupper($request->get('abrev'));
            $pais->setAbrev($paisAbrev);
        }
        
        if ($request->get('activo')) {
            $paisActivo = $request->get('activo');
            $pais->setActivo($paisActivo);
        }
        
        $em->flush($pais);

        $paisGuardado = $em->getRepository(Pais::class)->findOneById($paisId);
        
        $nombrePais = $paisGuardado->getDescripcion();

        $this->addFlash('success', "El país $nombrePais ha sido guardado");   

          
        return $paisGuardado;
        
    
    }

}
