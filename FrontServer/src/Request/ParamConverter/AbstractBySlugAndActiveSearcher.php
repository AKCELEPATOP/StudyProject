<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 30.01.2019
 * Time: 16:54
 */

namespace App\Request\ParamConverter;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractBySlugAndActiveSearcher
{
    /**
     * @param ParamConverter $configuration
     * @param Registry|null $registry
     *
     * @return bool
     */
    final protected function isObjectSupported(ParamConverter $configuration, Registry $registry = null)
    {
        if ($configuration->getConverter() !== $this->getConverterName()) {
            return false;
        }
        if (!$this->isEntityManagerFound($registry)) {
            return false;
        }
        $className = $configuration->getClass();
        if ($className === null) {
            return false;
        }
        $em = $registry->getManagerForClass($className);
        if ($em->getClassMetadata($className)->getName() !== $this->getEntityClassName()) {
            return false;
        }
        return true;
    }
    /**
     * @param Registry|null $registry
     *
     * @return bool
     */
    private function isEntityManagerFound(Registry $registry = null)
    {
        return null !== $registry && count($registry->getManagers()) > 0;
    }
    /**
     * @param ObjectRepository $repository
     * @param string $slug
     *
     * @return object
     *
     * @throws NotFoundHttpException
     */
    protected function getEntity(ObjectRepository $repository, $slug)
    {
        $entity = $repository->findOneBy([
            'slug' => $slug,
        ]);
        if ($entity === null) {
            throw new NotFoundHttpException();
        }
        return $entity;
    }
    /**
     * @return string
     */
    abstract protected function getEntityClassName();
    /**
     * @return string
     */
    abstract protected function getConverterName();
}