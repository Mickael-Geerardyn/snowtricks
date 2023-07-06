<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Groupe;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FigureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
				"label" => "Nom de la figure",
				"required" => true,
			])
            ->add('description', TextareaType::class, [
				"label" => "Description de la figure",
				"required" => true,
			])
			->add('groupe', EntityType::class, [
				'label' => 'Groupe',
				'class' => Groupe::class,
				'choice_label' => 'name',
				'required' => true,
				'multiple' => false,
				'expanded' => true,
			])
			->add('image', FileType::class, [
				'label' => "Insérer l'image",
				'mapped' => false,
				'multiple' => true,
				'required' => false,

			])
			->add('video', TextType::class, [
				"label" => 'Lien de la vidéo',
				'mapped' => false,
				'required' => false,
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
