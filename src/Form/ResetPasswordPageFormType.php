<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordPageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
				'label' => "Mot de passe",
				'mapped' => false,
				'attr' => ['autocomplete' => 'new-password'],
				'constraints' => [
					new NotBlank([
									 'message' => 'Veuillez entrer un mot de passe valide',
								 ]),
					new Length([
								   'min' => 6,
								   'minMessage' => 'Votre mot de passe doit contenir au minimum {{ limit }} caractères',
								   // max length allowed by Symfony for security reasons
								   'max' => 4096,
							   ]),
				],
			])
            ->add('confirmationPassword', PasswordType::class, [
		'label' => "Confirmer le mot de passe",
		'mapped' => false,
		'attr' => ['autocomplete' => 'new-password'],
		'constraints' => [
			new NotBlank([
							 'message' => 'Veuillez confirmer le mot de passe',
						 ]),
			new Length([
						   'min' => 6,
						   'minMessage' => 'Votre mot de passe doit contenir au minimum {{ limit }} caractères',
						   // max length allowed by Symfony for security reasons
						   'max' => 4096,
					   ]),
		],
	])
		;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
