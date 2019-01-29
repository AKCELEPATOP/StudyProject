<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 29.01.2019
 * Time: 10:38
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity*/
class Task
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $method;

    /**
     * @ORM\Column(type="string")
     */
    protected $url;

    /**
     * @ORM\Column(type="integer")
     */
    protected $speed;

    /**
     * @ORM\Column(type="datеtime")
     */
    protected $timeExecute;

    /**
     * @ORM\Column(type="string")
     */
    protected $body;

    public function __construct()
    {
        parent::__construct();
    }
}