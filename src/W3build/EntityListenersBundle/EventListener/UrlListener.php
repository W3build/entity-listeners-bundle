<?php
/**
 * Created by PhpStorm.
 * User: lukas_jahoda
 * Date: 17.1.15
 * Time: 20:16
 */

namespace W3build\EntityListenersBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use W3build\EntityListenersBundle\Component\String;
use W3build\EntityListenersBundle\Interfaces\UrlInterface;
use Doctrine\ORM\EntityManager;

class UrlListener implements EventSubscriber
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::preUpdate,
            Events::prePersist,
            Events::preRemove
        );
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs){
        $entity = $lifecycleEventArgs->getEntity();

        $this->entityManager = $lifecycleEventArgs->getEntityManager();
        if(!$entity instanceof UrlInterface){
            return;
        }

        if($entity->getUrl()){
            return;
        }

        $url = $this->findFreeUrl($entity, String::toUrl($entity->getTitle()));
        $entity->setUrl($url);
    }

    public function preUpdate(LifecycleEventArgs $lifecycleEventArgs){
        $entity = $lifecycleEventArgs->getEntity();

        $this->entityManager = $lifecycleEventArgs->getEntityManager();
        if(!$entity instanceof UrlInterface){
            return;
        }

        $changeSet = $this->entityManager->getUnitOfWork()->getEntityChangeSet($entity);

        if(isset($changeSet['url'])){
            $url = $this->findFreeUrl($entity, String::toUrl($entity->getUrl()));
            $entity->setUrl($url);
        }
    }

    private function findFreeUrl(UrlInterface $entity, $url, $increment = 0){
        $testUrl = $url;

        if($increment){
            $testUrl = $testUrl . '-' . $increment;
        }
        if($this->urlExists($entity, $testUrl)){
            $increment += 1;
            return $this->findFreeUrl($entity, $url, $increment);
        }

        return $testUrl;
    }

    private function urlExists(UrlInterface $entity, $url){
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('entity')
            ->from(get_class($entity), 'entity')
            ->where('entity.url = :url')
            ->setParameter('url', $url);

        if($entity->getId()){
            $queryBuilder->andWhere('entity.id != ' . $entity->getId());
        }

        if($queryBuilder->getQuery()->getOneOrNullResult()){
            return true;
        }

        return false;
    }

}