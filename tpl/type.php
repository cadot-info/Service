<?php

namespace App\src\CMService\tpl;

use App\Entity\¤Entity¤;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//¤uses¤

class ¤Entity¤Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $AtypeOption)
    {
        //¤adds¤;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ¤Entity¤::class,
        ]);
    }
}
