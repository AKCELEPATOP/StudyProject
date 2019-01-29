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
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BaseAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $roles = $container->getParameter('security.role_hierarchy.roles');

        $rolesChoices = self::flattenRoles($roles);

        $formMapper
            ->with('General')
//            ->add('username')
//            ->add('email')
//            ->add('enabled', null, array('required' => false))
//            ->add('roles', ChoiceType::class, array(
////                'data'  => $rolesChoices,
//                'label' => 'config.label_type',
//                'choices' => $rolesChoices,
//                'required' => false
//            ))
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Yes' => 'stock_yes',
                    'No' => 'stock_no',
                ),
            ))
            //->add('plainPassword', 'text', array('required' => false))
            ->end();

//        if (!$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
//            $formMapper
//                ->with('Management')
//                ->add('roles', 'sonata_security_roles', array(
//                    'expanded' => true,
//                    'multiple' => true,
//                    'required' => false
//                ))
//                ->add('enabled', null, array('required' => false))
//                ->end();
//        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id')
            ->add('username')
            ->add('enabled')
            ->add('email');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
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
        foreach($rolesHierarchy as $roles) {

            if(empty($roles)) {
                continue;
            }

            foreach($roles as $role) {
                if(!isset($flatRoles[$role])) {
                    $flatRoles[$role] = $role;
                }
            }
        }

        return $flatRoles;
    }
}