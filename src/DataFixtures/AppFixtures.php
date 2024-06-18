<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $newPost = new MicroPost();
        $newPost->setText('This is a first post');
        $newPost->setDateTime(new DateTime());
        ;
        $newPost->setTitle('First Post');
        $manager->persist($newPost);

        $newPost2 = new MicroPost();
        $newPost2->setText('This is a first post 2');
        $newPost2->setDateTime(new DateTime());
        $newPost2->setTitle('First Post 2');
        $manager->persist($newPost2);

        $manager->flush();
    }
}
