<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Mark;
use Doctrine\ORM\EntityManagerInterface;

class MarkPersister implements DataPersisterInterface
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports($data): bool
    {
        return $data instanceof Mark;
    }

    public function persist($data)
    {
        $this->em->persist($data);
        $this->em->flush();
    }

    public function remove($data)
    {
        $this->em->remove($data);
        $this->em->flush();
    }
}
