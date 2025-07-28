<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\CampaignStructure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CampaignStep2StructureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('font', ChoiceType::class, [
                'choices' => [
                    'Oswald-Regular' => 'font/Oswald/static/Oswald-Regular.ttf',
                    'Oswald-Italic' => 'font/Oswald/static/Oswald-Italic.ttf',
                    'Oswald-Bold' => 'font/Oswald/static/Oswald-Bold.ttf',
                    'Arimo-Regular' => 'font/Arimo/static/Arimo-Regular.ttf',
                    'Arimo-Italic' => 'font/Arimo/static/Arimo-Italic.ttf',
                    'Arimo-Bold' => 'font/Arimo/static/Arimo-Bold.ttf',
                ],
            ])
            ->add('colorR')
            ->add('colorG')
            ->add('colorB')
            ->add('shadowXShift')
            ->add('shadowYShif')
            ->add('fontSize')
            ->add('lineHeight')
            ->add('boxX')
            ->add('boxY')
            ->add('boxWidth')
            ->add('boxHeight',IntegerType::class,[
                'attr' => [
                    'type' => 'range',
                    'min' => 0,   // Defina o valor mínimo do range
                    'max' => 1080, // Defina o valor máximo do range
                    'step' => 1   // Defina o valor do incremento
                ],
                'label' => 'Slider',
            ])
            ->add('alignX', ChoiceType::class, [
                'choices' => [
                    'left' => 'left',
                    'right' => 'right',
                    'center' => 'center',
                ],
            ])
            ->add('alignY', ChoiceType::class, [
                'choices' => [
                    'top' => 'top',
                    'bottom' => 'bottom',
                    'center' => 'center',
                ],
            ])
            ->add('title')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampaignStructure::class,
        ]);
    }
}
