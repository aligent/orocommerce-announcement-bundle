<?php
/**
 * @category  Aligent
 * @package
 * @author    Jan Plank <jan.plank@aligent.com.au>
 * @copyright 2021 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\Form\Type;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\CustomerBundle\Entity\Repository\CustomerGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerGroupMultiSelectType extends AbstractType
{
    const NAME = 'aligent_customer_group_multiselect';

    protected ObjectManager $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'class' => CustomerGroup::class,
                'multiple' => true,
                'required' => false,
            ]
        );

        $resolver->setNormalizer(
            'choices',
            function (OptionsResolver $options) {
                /** @var CustomerGroupRepository $repo */
                $repo = $this->manager->getRepository(CustomerGroup::class);

                $customerGroups = $repo->findAll();
                $result = [];
                /** @var CustomerGroup $customerGroup */
                foreach ($customerGroups as $customerGroup) {
                    $label = $customerGroup->getName();
                    $result[$label] = $customerGroup->getId();
                }
                return $result;
            }
        );
    }

    public function getName(): ?string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): ?string
    {
        return self::NAME;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
