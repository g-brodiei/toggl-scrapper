<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TogglReportForm extends AbstractType
{

  public function configureOptions(OptionsResolver $resolver){

    $resolver->setDefaults([
      'workspaceOptions' => [],
      'userId' => [],
    ]);
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('Workspace_list', ChoiceType::class, [
        'choices' => $options['workspaceOptions'],
        'mapped' => false,
      ])
      ->add('User_id', TextType::class, [
        'data' => $options['userId'],
        'mapped' => false
      ])
      ->add('Since', DateType::class)
      ->add('until', DateType::class)
      ->add('submit_2', SubmitType::class);
  }
}
