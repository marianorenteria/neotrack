<?php
// src/Form/ZohoCRMType.php
namespace App\Form;

use App\Entity\ZohoCRM;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ZohoCRMType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('clientId', TextType::class, [
                'label' => 'Client ID',
                'attr' => [
                    'placeholder' => 'Enter your Zoho CRM Client ID',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Zoho CRM Client ID',
                    ]),
                ],
            ])
            ->add('clientSecret', TextType::class, [
                'label' => 'Client Secret',
                'attr' => [
                    'placeholder' => 'Enter your Zoho CRM Client Secret',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Zoho CRM Client Secret',
                    ]),
                ],
            ])
            ->add('apiToken', TextType::class, [
                'label' => 'API Token',
                'attr' => [
                    'placeholder' => 'Enter your Zoho CRM API Token',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Zoho CRM API Token',
                    ]),
                ],
            ])
            ->add('refreshToken', TextType::class, [
                'label' => 'Refresh Token',
                'attr' => [
                    'placeholder' => 'Enter your Zoho CRM Refresh Token',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Zoho CRM Refresh Token',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ZohoCRM::class,
        ]);
    }
}
