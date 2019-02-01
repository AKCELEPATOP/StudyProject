<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 30.01.2019
 * Time: 16:55
 */

namespace App\Request\ParamConverter;


use App\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class TaskParamConverter extends  AbstractBySlugAndActiveSearcher implements ParamConverterInterface
{

    protected $registry;

    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Task::class;
    }

    /**
     * @return string
     */
    protected function getConverterName()
    {
        return 'task_converter';
    }

    /**
     * Stores the object in the request.
     *
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $taskSlug = $request->attributes->get();
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        // TODO: Implement supports() method.
    }
}