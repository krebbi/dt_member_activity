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



    public function __construct()
    {

    }

    public function setPath($path)
    {
        $this->ilPath = $path;
    }


    public static function getByPath($path)
    {
        $em = \ORM::entityManager();
        return $em->getRepository(get_class())->findBy(
            ['ilPath' => $path],
            ['ilID' => 'ASC']
        );
    }


    public function remove()
    {
        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }
}
