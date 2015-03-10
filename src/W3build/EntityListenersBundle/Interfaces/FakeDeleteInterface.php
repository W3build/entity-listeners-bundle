<?php
/**
 * Created by PhpStorm.
 * User: lukas_jahoda
 * Date: 18.1.15
 * Time: 5:21
 */

namespace W3build\EntityListenersBundle\Interfaces;


interface FakeDeleteInterface {

    public function getId();

    public function setDeleted();

}