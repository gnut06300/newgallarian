<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;


class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //dd($options);
        if ($options['edit']==true) { // if ($options['edit']==true AND $option['user']['roles']=="ROLE_ADMIN")
            $builder
            ->setMethod("PATCH")
            ->add('isActived', CheckboxType::class,[
                'required'=>false
            ]);

        }

        $builder
            ->add('name', TextType::class, ['attr'=>['placeholder'=>'Entrez le nom de la Catégories', 'class'=>'test'],
                'label'=>'Entrez le nom de la Catégories',
                'required' => false, //Enlève la validation HTML pour ce champ là

            ])
            ->add('content', TextareaType::class, [
                'required' => false,//Enlève la validation HTML pour ce champ là
                'attr'=>['style'=>'height:200px; width:300px;',
                'placeholder'=>'Entrez un commentaire de la Catégories'
                ],
                'label'=>'Entrez un commentaire',
                'constraints'=>[
                    new Length([
                        "min"=>5,
                        "max"=>255,
                        "minMessage"=>"La description doit contenir {{ limit }} caractères",
                        "maxMessage"=>"La description doit avoir au maximum {{ limit }} caractéres"
                    ])
                ]
            ])
            /*->add('test', TextType::class, [
                "mapped"=>false
            ])*/
           
            
        
            ->add('Valider', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'edit'=>false // a ajouter
        ])
        ->setAllowedTypes("edit","bool");// a ajouter
    }
}
