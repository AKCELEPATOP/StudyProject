<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 29.01.2019
 * Time: 10:41
 */

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BaseAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $roles = $container->getParameter('security.role_hierarchy.roles');

        $rolesChoices = self::flattenRoles($roles);

        $formMapper
            ->with('General')
            ->add('username')
            ->add('password')
            ->add('email')
            ->add('enabled', null, array('required' => false))
            ->add('roles', ChoiceType::class, array(
//                'data'  => $rolesChoices,
                'choices' => ['ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN', 'ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER'],
                'multiple' => true,
                'required' => false
            ))
            //->add('plainPassword', 'text', array('required' => false))
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id')
            ->add('username')
            ->add('password')
            ->add('enabled')
            ->add('email');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('password')
            ->add('email')
            ->add('enabled', null, array('editable' => true))
            ->add('createdAt');

    }

    /**
     * @param string $baseRouteName
     */
    public function setBaseRouteName(string $baseRouteName): void
    {
        $this->baseRouteName = $baseRouteName;
    }

    protected static function flattenRoles($rolesHierarchy)
    {
        $flatRoles = array();
        foreach ($rolesHierarchy as $roles) {

            if (empty($roles)) {
                continue;
            }

            foreach ($roles as $role) {
                if (!isset($flatRoles[$role])) {
                    $flatRoles[$role] = $role;
                }
            }
        }

        return $flatRoles;
    }
}