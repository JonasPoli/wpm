<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\Enum\LanguageEnum;
use App\Entity\Enum\MidiaEnum;
use App\Entity\ProductCategory;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scheduleDate', null, [
                'widget' => 'single_text',
            ])
            ->add('imageFile',VichFileType::class,[
                'required' => false,
                'allow_delete' => false,
                'asset_helper' => false,
                'download_uri' => false,

            ])
            ->add('midia',ChoiceType::class,[
                'choices' => MidiaEnum::getOptions(),
            ])
            ->add('campaign', EntityType::class, [
                'class' => Campaign::class,
                'choice_label' => 'name',
            ])
            ->add('campaign', EntityType::class, [
                'class' => Campaign::class,
                'choice_label' => function(Campaign $campaign){
                    return $campaign->getName().' - '.$campaign->getClient()->getName();
                },
            ])
            ->add('createdBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
            ])
            ->add('approvedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
