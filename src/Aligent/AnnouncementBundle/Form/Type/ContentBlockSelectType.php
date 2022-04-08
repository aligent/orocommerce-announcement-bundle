<?php
/**
 * @category  Aligent
 * @package
 * @author    Greg Ziborov <greg.ziborov@aligent.com.au>
 * @copyright 2021 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */


namespace Aligent\AnnouncementBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Oro\Bundle\CMSBundle\Entity\ContentBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Add form field with content blocks select list
 */
class ContentBlockSelectType extends AbstractType
{
    const NAME = 'aligent_content_blocks_select';

    /** @var ManagerRegistry */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'class' => ContentBlock::class,
                'multiple' => false,
                'required' => false,
            ]
        );

        $resolver->setNormalizer(
            'choices',
            function (OptionsResolver $options) {
                /** @var EntityRepository $repo */
                $repo = $this->registry
                    ->getManagerForClass(ContentBlock::class)
                    ->getRepository(ContentBlock::class);

                $contentBlocks = $repo->findAll();
                $result = [];
                foreach ($contentBlocks as $contentBlock) {
                    $label = $contentBlock->getAlias();
                    $result[$label] = $contentBlock->getAlias();
                }
                return $result;
            }
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
