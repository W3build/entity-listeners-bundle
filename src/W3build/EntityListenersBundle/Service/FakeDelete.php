<?php
namespace W3build\EntityListenersBundle\Service;

use Doctrine\ORM\EntityManager;
use W3build\EntityListenersBundle\Interfaces\FakeDeleteInterface;

class FakeDelete {

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    public function fakeDelete(FakeDeleteInterface $entity){
        $entity->setDeleted();

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

}