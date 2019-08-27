<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TypeController
 * @package App\Controller
 *
 * @Route("/type")
 */
class TypeController extends AbstractController
{

    /**
     * @Route("/", name="type_index")
     */
    public function index()
    {
        return $this->render('type/index.html.twig', [
            'controller_name' => 'TypeController',
        ]);
    }

    /**
     * @Route("/new", name="type_create", methods={"POST", "GET"})
     * @param Request $request
     * @param ObjectManager $entityManager
     * @param TypeRepository $typeRepository
     * @return Response
     */
    public function create(Request $request,
                           ObjectManager $entityManager,
                           TypeRepository $typeRepository)
    {

        $type = new Type();

        $form = $this->createForm(TypeType::class, $type);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $entityManager->persist($type);
            $entityManager->flush();
        }

        return $this->render('type/create.html.twig', [
            'form'  => $form->createView(),
            'types' => $typeRepository->findAll()
        ]);

    }

    /**
     * @Route("/{id}/delete", name="type_delete", methods={"DELETE"})
     * @param Type $type
     */
    public function delete(Request $request, Type $type)
    {
        $token = $request->request->get('_token');

        if ( $this->isCsrfTokenValid('deleteType' . $type->getId(), $token) )
        {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($type);
            $entityManager->flush();

            return $this->redirectToRoute('type_create');

        }
    }
}
