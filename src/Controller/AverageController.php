<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AverageController extends AbstractController
{
    /**
     * @Route("/average", name="average")
     */
    public function view(): Response
    {
        return $this->render('average/average.html.twig');
    }
}
