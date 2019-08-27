<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function create()
    {

        $type = new Type();

        $form = $this->createForm(TypeType::class, $type);

        return $this->render('type/create.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
