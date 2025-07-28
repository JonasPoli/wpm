<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\CampaignStructure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignStructureType extends AbstractType
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
            ->add('boxHeight')
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
            ->add('campaign', EntityType::class, [
                'class' => Campaign::class,
                'choice_label' => function(Campaign $campaign){
                    return $campaign->getName().' - '.$campaign->getClient()->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampaignStructure::class,
        ]);
    }
}
