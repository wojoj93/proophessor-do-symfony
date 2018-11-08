<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PostThankYouType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('giver_id', TextType::class, ['required' => true]);
        $builder->add('reason', TextareaType::class, ['required' => true]);
        $builder->add('amount', IntegerType::class, ['required' => true]);
        $builder->add('receivers_ids', CollectionType::class, ['required' => true, 'allow_add' => true, 'delete_empty' => true]);
    }
}
