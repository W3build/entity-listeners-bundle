<?php
/**
 * Created by PhpStorm.
 * User: lukas_jahoda
 * Date: 17.1.15
 * Time: 20:16
 */

namespace W3build\EntityListenersBundle\EventListener;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use W3build\EntityListenersBundle\Interfaces\PerexInterface;
use W3build\EntityListenersBundle\UrlInterface;

class PerexListener implements EventSubscriber
{

    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader){
        $this->annotationReader = $annotationReader;
    }

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
        );
    }

    private function createPerex($entity){
        if(!$entity instanceof PerexInterface){
            return;
        }

        if($entity->getPerex()){
            return;
        }

        $reflectionObject = new \ReflectionClass(get_class($entity));

        $length = 180;
        $useDots = true;
        foreach ($reflectionObject->getProperties() as $property){
            /** @var \W3build\EntityListenersBundle\Annotation\Perex $annotaton */
            if($annotaton = $this->annotationReader->getPropertyAnnotation($property, 'W3build\EntityListenersBundle\Annotation\Perex')){
                $length = $annotaton->getMaxLength();
                $useDots = $annotaton->isUseDots();
            }
        }

        $content = html_entity_decode(strip_tags($entity->getContent()));
        if(strlen($content) >= $length){
            if($useDots){
                $entity->setPerex(substr($content, 0, strrpos(substr($content, 0, $length - 2), ' ')) . '..');
            }
            else {
                $entity->setPerex(substr($content, 0, strrpos(substr($content, 0, $length), ' ')));
            }
        }
        else {
            $entity->setPerex($content);
        }
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs){
        $entity = $lifecycleEventArgs->getEntity();

        return $this->createPerex($entity);
    }

    public function preUpdate(LifecycleEventArgs $lifecycleEventArgs){
        $entity = $lifecycleEventArgs->getEntity();

        return $this->createPerex($entity);
    }
}