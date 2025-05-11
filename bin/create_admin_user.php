<?php
// create_admin_user.php

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

require dirname(__DIR__).'/vendor/autoload_runtime.php';

return function(array $context) {
    $kernel = $context['kernel'];
    $em = $kernel->getContainer()->get('doctrine')->getManager();
    $passwordHasher = $kernel->getContainer()->get(UserPasswordHasherInterface::class);
    
    // Create admin user
    $user = new User();
    $user->setEmail('admin@example.com');
    $user->setRoles(['ROLE_ADMIN']);
    $hashedPassword = $passwordHasher->hashPassword($user, 'admin123');
    $user->setPassword($hashedPassword);
    
    $em->persist($user);
    $em->flush();
    
    echo "Admin user created: admin@example.com (password: admin123)\n";
    return 0;
};
