<?php

namespace App\Twig;

use App\Entity\Enum\MidiaEnum;
use App\Entity\Financing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class FinancingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('shortDescription')
            ->add('fullDescription')
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Ativo' => 1,
                    'Inativo' => 0,
                ],
            ])
            ->add('highlighted', ChoiceType::class, [
                'choices'  => [
                    'Ativo' => 1,
                    'Inativo' => 0,
                ],
            ])
            ->add('position')
            ->add('imageFile', VichFileType::class,[
                'required' => false,
                'allow_delete' => false,
                'asset_helper' => false,
                'download_uri' => false,
            ])
            ->add('language',ChoiceType::class,[
                'choices' => MidiaEnum::getOptions(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Financing::class,
        ]);
    }
}
