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

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\CMSBundle\Entity\ContentBlock;
use Oro\Bundle\CMSBundle\Entity\Repository\ContentBlockRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Add form field with content blocks select list
 */
class ContentBlockSelectType extends AbstractType
{
    const NAME = 'aligent_content_blocks_select';

    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function configureOptions(OptionsResolver $resolver): void
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
                /** @var ContentBlockRepository $repo */
                $repo = $this->registry->getRepository(ContentBlock::class);

                $contentBlocks = $repo->findAll();
                $result = [];
                /** @var ContentBlock $contentBlock */
                foreach ($contentBlocks as $contentBlock) {
                    $label = $contentBlock->getAlias();
                    $result[$label] = $contentBlock->getAlias();
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
