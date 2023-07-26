<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('path', FileType::class, [
				"label" => "Image",
				"mapped" => false,
				'constraints' => [
					new File([
						'maxSize' => '1024k',
						'mimeTypes' => [
							  'image/jpeg',
							  'image/png',
							],
						'mimeTypesMessage' => 'Merci de télécharger un document valide (JPEG ou PNG)',
								 ])
				],
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
