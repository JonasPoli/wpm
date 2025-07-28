<?php

namespace App\Form;

use App\Entity\Client;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('companyType')
            ->add('activityType')
            ->add('email')
            ->add('phone')
            ->add('facebookPageId')
            ->add('address')
            ->add('logoFile',VichFileType::class,[
                'required' => false,
                'allow_delete' => false,
                'asset_helper' => false,
                'download_uri' => false,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
