<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\Enum\LanguageEnum;
use App\Entity\Enum\MidiaEnum;
use App\Entity\ProductCategory;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostStep5Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('textToPublish', TextareaType::class,[
                'required' => false,
            ])
            ->add('scheduleDate', null, [
                'widget' => 'single_text',
            ])
            ->add('imageFile',VichFileType::class,[
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
            'data_class' => Post::class,
        ]);
    }
}
