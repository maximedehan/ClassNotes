<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarkController extends AbstractController
{
    /**
     * @Route("/mark", name="mark")
     */
    public function view(): Response
    {
        return $this->render('mark/mark.html.twig');
    }
}
