<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\BlogPost;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encode)
    {
        $this->passwordEncoder = $encode;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {


        $this->loadUser($manager);
        $this->loadBlogPost($manager);
        $this->loadComment($manager);

    }

    public function loadBlogPost(ObjectManager $manager)
    {

        $user = $this->getReference('user_admin');


        for($i = 0 ; $i<100 ; $i++){


            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30))
            ->setContent($this->faker->realText())
            ->setPublished($this->faker->dateTimeThisYear)
            ->setAuthor($user)
            ->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i",$blogPost);

            $manager->persist($blogPost);
            $manager->flush();
    
    
    


        }


    }

    public function loadUser(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin')
        ->setEmail('atud@free.fr')
        ->setName('nico')
        ->setPassword($this->passwordEncoder->encodePassword($user,"lksjflksjdfl"));

        //add refeernce
        $this->addReference('user_admin',$user);


        $manager->persist($user);
        $manager->flush();
    
    }

    public function loadComment(ObjectManager $manager)
    {


        for($i = 0; $i <100; $i++){
            for ($j = 0 ; $j< rand(1,10); $j++){
                $comment = new Comment();
                $comment->setContent($this->faker->realText())
                ->setPublished($this->faker->dateTimeThisYear)
                ->setAuthor($this->getReference('user_admin'))
                ->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
                $manager->flush();
            }
        }

    }
}
