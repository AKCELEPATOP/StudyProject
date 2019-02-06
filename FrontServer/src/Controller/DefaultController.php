<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 04.02.2019
 * Time: 14:28
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    private $filename = "angular.html";

    public function index()
    {
        return $this->render("angular/angular.html.twig");
    }
}