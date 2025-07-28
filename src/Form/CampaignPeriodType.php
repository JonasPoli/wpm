<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CampaignPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Outros campos do formulário
            ->add('periodicidade', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    '2 x por semana (Terça e Quinta)' => '2_weekly_tue_thu',
                    '3 x por semana (Segunda, Quarta e Sexta)' => '3_weekly_mon_wed_fri',
                    '4 x por semana (Segunda, Terça, Quinta e Sexta)' => '4_weekly_mon_tue_thu_fri',
                    '1 x por semana (Quarta-feira)' => '1_weekly_wed',
                    '5 x por semana (Segunda a Sexta)' => '5_weekly_mon_fri',
                    '2 x por semana (Segunda e Sexta)' => '2_weekly_mon_fri',
                    '3 x por semana (Terça, Quinta e Sábado)' => '3_weekly_tue_thu_sat',
                    '6 x por semana (Segunda a Sábado)' => '6_weekly_mon_sat',
                    '1 x por dia (Segunda a Domingo)' => '7_weekly_mon_sun',
                    '4 x por semana (Domingo, Terça, Quinta e Sábado)' => '4_weekly_sun_tue_thu_sat',
                ],
                'placeholder' => 'Selecione a periodicidade',
                'label' => 'Periodicidade',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configurações padrão
        ]);
    }
}
