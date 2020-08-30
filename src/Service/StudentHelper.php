<?php

namespace App\Service;

use App\Entity\Student;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class StudentHelper
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getStudentFromFormRequest(Request $request)
    {
        // On récupère les données saisies par l'utilisateur
        $data = $request->request->get('form');

        // On récupère l'entité student générée par les données du formulaire
        return $this->serializer->deserialize(json_encode($data),Student::class,'json');
    }
}
