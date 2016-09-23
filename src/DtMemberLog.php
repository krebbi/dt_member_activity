<?php
namespace Concrete\Package\DtMemberActivity\Src;

use Doctrine\DBAL\Connection;
use Page;
use Database;
use File;
use Core;
use User;


/**
 * @Entity
 * @Table(name="DtMemberActivityLog")
 */
class DtMemberLog
{
    /** 
     * @Id @Column(type="integer") 
     * @GeneratedValue 
     */
    protected $lID;

    /**
     * @Column(type="integer",nullable=false)
     */
    protected $luID;

    /**
     * @Column(type="text",nullable=false)
     */
    protected $luName;

    /**
     * @Column(type="text",nullable=false)
     */
    protected $luIP;

    /**
     * @Column(type="text",nullable=false)
     */
    protected $luEmail;

    /**
     * @Column(type="datetime")
     */
    protected $lDate;

    /**
     * @Column(type="text",nullable=false)
     */
    protected $lType;

    /**
     * @Column(type="integer",nullable=false)
     */
    protected $lTypeID;

    /**
     * @Column(type="text",nullable=false)
     */
    protected $lTypeName;

    /**
     * @Column(type="text",nullable=false)
     */
    protected $lTypePath;



    public function __construct()
    {

    }

    public function setUserID($uID)
    {
        $this->luID = $uID;
    }

    public function setIP($uIP)
    {
        $this->luIP = $uIP;
    }

    public function setUserName($uName)
    {
        $this->luName = $uName;
    }

    public function setUserEmail($email)
    {
        $this->luEmail = $email;
    }

    public function setDate($date)
    {
        $this->lDate = $date;
    }

    public function setType($type)
    {
        $this->lType = $type;
    }

    public function setTypeID($id)
    {
        $this->lTypeID = $id;
    }

    public function setTypeName($name)
    {
        $this->lTypeName = $name;
    }

    public function setTypePath($path)
    {
        $this->lTypePath = $path;
    }



    public static function getByID($lID)
    {
        $em = \ORM::entityManager();
        return $em->find(get_class(), $lID);
    }


    public static function getLastUpdate($type, $typeID)
    {
        $em = \ORM::entityManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select(array('s'))
            ->from(get_class(), 's')
            ->where('s.lType = :type')
            ->andWhere('s.lTypeID = :typeID')
            ->andWhere('s.lChange IS NOT NULL')
            ->setParameter('type', $type)
            ->setParameter('typeID', $typeID)
            ->addOrderBy('s.lID', 'DESC')
            ->setMaxResults( 1 )
            ->getQuery();

        return $query->getResult();
    }

    public static function getLastTypeIdUpdate($typeID, $max)
    {
        $em = \ORM::entityManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select(array('s'))
            ->from(get_class(), 's')
            ->where('s.lTypeID = :TypeID')
            ->setParameter('TypeID', $typeID)
            ->addOrderBy('s.lID', 'DESC')
            ->setMaxResults( $max )
            ->getQuery();

        return $query->getResult();
    }


    public static function getLastActivityByUser($uID)
    {
        $em = \ORM::entityManager();
        return $em->getRepository(get_class())->findOneBy(
            ['luID' => $uID],
            ['lID' => 'DESC']
        );
    }

    public static function getActivityByUser($uID)
    {
        $em = \ORM::entityManager();
        return $em->getRepository(get_class())->findBy(
            ['uID' => $uID],
            ['lID' => 'ASC']
        );
    }



    public function getID()
    {
        return $this->lID;
    }

    public function getUserID()
    {
        return $this->luID;
    }

    public function getIP()
    {
        return $this->luIP;
    }

    public function getDate()
    {
        return $this->lDate;
    }

    public function getTypeID()
    {
        return $this->lTypeID;
    }

    public function getType()
    {
        return h($this->lType);
    }

    public function getTypeName()
    {
        return h($this->lTypeName);
    }

    public function getTypePath()
    {
        return h($this->lTypePath);
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
