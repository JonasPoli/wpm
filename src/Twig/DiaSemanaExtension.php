<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DiaSemanaExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('dia_da_semana', [$this, 'diaDaSemana']),
        ];
    }

    public function diaDaSemana(\DateTimeInterface $date): string
    {
        $diasDaSemana = [
            'Domingo',
            'Segunda-feira',
            'Terça-feira',
            'Quarta-feira',
            'Quinta-feira',
            'Sexta-feira',
            'Sábado',
        ];

        return $diasDaSemana[(int) $date->format('w')];
    }
}
