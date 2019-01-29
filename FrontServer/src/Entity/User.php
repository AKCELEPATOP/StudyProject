<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 28.01.2019
 * Time: 17:10
 */

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity*/
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="user")
     */
    protected $tasks;


    public function __construct()
    {
        parent::__construct();
    }
}