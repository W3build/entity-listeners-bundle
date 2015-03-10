<?php
namespace W3build\EntityListenersBundle\Annotation;

/**
 * Class Perex
 * @package W3build\EntityListenersBundle\Annotation
 *
 * @Annotation
 */
class Perex {

    private $maxLength = 180;

    private $useDots = true;

    public function __construct($data){
        if(isset($data['maxLength']) && $data['maxLength']){
            $this->maxLength = (int) $data['maxLength'];
        }

        if(isset($data['useDots'])){
            $this->useDots = $data['useDots'];
        }
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @return boolean
     */
    public function isUseDots()
    {
        return $this->useDots;
    }

}