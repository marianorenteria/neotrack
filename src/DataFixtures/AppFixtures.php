<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Company ABC
        $companyABC = new Company();
        $companyABC->setName('Company ABC');
        $companyABC->setAddress('123 Main Street, City, Country');
        $companyABC->setPhone('(123) 456-7890');
        $companyABC->setWebsite('https://www.companyabc.com');
        $manager->persist($companyABC);
        
        // Create Company XYZ (second company)
        $companyXYZ = new Company();
        $companyXYZ->setName('Company XYZ');
        $companyXYZ->setAddress('456 Park Avenue, Metro City, Country');
        $companyXYZ->setPhone('(987) 654-3210');
        $companyXYZ->setWebsite('https://www.companyxyz.com');
        $manager->persist($companyXYZ);
        
        // Create admin user associated with Company ABC
        $adminUser = new User();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->passwordHasher->hashPassword(
            $adminUser,
            'admin123'
        ));
        $adminUser->setCompany($companyABC); // Link to Company ABC
        $adminUser->setFullName('John Smith'); // Add full name
        $manager->persist($adminUser);
        
        // Create second admin user associated with Company XYZ
        $adminUser2 = new User();
        $adminUser2->setEmail('admin2@example.com');
        $adminUser2->setRoles(['ROLE_ADMIN']);
        $adminUser2->setPassword($this->passwordHasher->hashPassword(
            $adminUser2,
            'admin456'
        ));
        $adminUser2->setCompany($companyXYZ); // Link to Company XYZ
        $adminUser2->setFullName('Sarah Johnson'); // Add full name
        $manager->persist($adminUser2);
        
        // Create a regular user associated with Company ABC
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'user123'
        ));
        $user->setCompany($companyABC); // Link to Company ABC
        $user->setFullName('Mike Wilson'); // Add full name
        $manager->persist($user);

        $manager->flush();
    }
}
