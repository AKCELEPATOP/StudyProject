<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 31.01.2019
 * Time: 9:44
 */

namespace App\Service;


use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

final class Validate
{
    private $validator;
    private $em;
    /**
     * Validate constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator=$validator;
    }
    public function validateRequest($data)
    {
        $errors = $this->validator->validate($data);
        $errorsResponse = array();
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $errorsResponse[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }
        if (count($errors))
        {
            $reponse=array(
                'code'=>1,
                'message'=>'validation errors',
                'errors'=>$errorsResponse,
                'result'=>null
            );
            return $reponse;
        }else{
            $reponse=[];
            return $reponse;
        }
    }

}