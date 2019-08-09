<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $em = $this->getDoctrine()->getManager();

        $provincia = $em->getRepository(Provincia::class)->findOneById($id);
     
        $form = $this->createFormBuilder($provincia)
        ->add('descripcion', TextType::class, ['label' => 'Nombre:',
        ])

        ->add('abrev', TextType::class, ['label' => 'Abreviación:',
        ])

        ->add('pais', EntityType::class, ['label' => 'Pais:',
        'class' => 'AppBundle:Pais',
        'query_builder' => function (EntityRepository $repositoryPais) {
                                return $repositoryPais->createQueryBuilder('paises')
                                          ->where('paises.activo LIKE :activo')
                                          ->setParameter('activo', '1')
                                          ->orderBy('paises.descripcion', 'ASC');
                             },
        'choice_label' => 'descripcion',
        ])

        ->add('activo', ChoiceType::class, ['label' => 'Estado:',
        'choices'  => ['ACTIVO' => '1', 'NO ACTIVO' => '0']])

        ->add('save', SubmitType::class, ['label' => 'Guardar',
        'attr' => ['class' => 'btn-style1 btn-center']])

        ->getForm();    

        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $provincia = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($provincia);
            $entityManager->flush();

            $nombreProvincia = $provincia->getDescripcion();

            $this->addFlash('success', "La provincia $nombreProvincia ha sido editada");

            $url= $this->generateUrl('detalleProvincia', [ 'id' => $id,]);
            return $this->redirect($url);

        }


        return $this->render('editarprovincia.html.twig', ['provincia' => $provincia, 'form' => $form->createView()  ]);
        
    }

    /**
     * @Route("/provincias/nueva", name="nuevaProvincia")
     */
    public function nuevaProvincia(Request $request)
    {
        $provincia = new Provincia();
        
        $form = $this->createFormBuilder($provincia)
                ->add('descripcion', TextType::class, ['label' => 'Nombre:',
                ])

                ->add('abrev', TextType::class, ['label' => 'Abreviación:',
                ])

                ->add('pais', EntityType::class, ['label' => 'Pais:',
                'class' => 'AppBundle:Pais',
                'query_builder' => function (EntityRepository $repositoryPais) {
                                        return $repositoryPais->createQueryBuilder('paises')
                                                  ->where('paises.activo LIKE :activo')
                                                  ->setParameter('activo', '1')
                                                  ->orderBy('paises.descripcion', 'ASC');
                                     },
                'choice_label' => 'descripcion',
                ])

                ->add('activo', ChoiceType::class, ['label' => 'Estado:',
                'choices'  => ['ACTIVO' => '1', 'NO ACTIVO' => '0']])

                ->add('save', SubmitType::class, ['label' => 'Guardar',
                'attr' => ['class' => 'btn-style1 btn-center']])

                ->getForm();    

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $provincia = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($provincia);
            $entityManager->flush();

            $nombreProvincia = $provincia->getDescripcion();

            $this->addFlash('success', "La provincia $nombreProvincia ha sido creada");

            return $this->redirectToRoute('provincias');

        }

        return $this->render('nuevaprovincia.html.twig', ['form' => $form->createView() ]);
        
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

        $provincia = $em->getRepository(Provincia::class)->find($id);
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
            $provincia->setAbrev($provinciaAbrev);
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
