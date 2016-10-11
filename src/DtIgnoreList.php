<?php
namespace Concrete\Package\DtMemberActivity\Src;

use Doctrine\DBAL\Connection;
use Page;
use Database;
use Core;
use User;


/**
 * @Entity
 * @Table(name="DtMemberIgnoreList")
 */
class DtIgnoreList
{
    /** 
     * @Id @Column(type="integer") 
     * @GeneratedValue 
     */
    protected $ilID;


    /**
     * @Column(type="text",nullable=false)
     */
    protected $ilPath;

    /**
     * @Column(type="boolean")
     */
    protected $ilExact;



    public function __construct()
    {

    }

    public function setPath($path)
    {
        $this->ilPath = $path;
    }

    public function setExact($bool)
    {
        $this->ilExact = $bool;
    }

    public function getID()
    {
        return $this->ilID;
    }

    public function getPath()
    {
        return $this->ilPath;
    }

    public function isExact()
    {
        return $this->ilExact;
    }

    public static function getByID($ilID)
    {
        $em = \ORM::entityManager();
        return $em->find(get_class(), $ilID);
    }

    public static function getAll()
    {
        $em = \ORM::entityManager();
        return $em->getRepository(get_class())->findAll();
    }

    public static function getByPath($path)
    {
        $em = \ORM::entityManager();
        return $em->getRepository(get_class())->findBy(
            ['ilPath' => $path],
            ['ilID' => 'ASC']
        );
    }

    public static function isListed($path)
    {
        $em = \ORM::entityManager();
        $allExcludes = $em->getRepository(get_class())->findAll();

        foreach ($allExcludes as $exclude) {
            if($exclude->isExact()) {
                if($path == $exclude->getPath()) $result = true;
            } else {
                if(strpos($path,$exclude->getPath()) !== false) $result = true;
            }
        }

        if ($result) return true; else return false;
    }

    public function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function remove()
    {
        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }
}
