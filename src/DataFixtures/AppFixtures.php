<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;
    /**
     * Password Hasher
     *
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->faker= Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        //Public
        $publicUser = new User();
        $password = $this->faker->password(2,6);
        $publicUser
            ->setUsername($this->faker->userName() . "@" . $password)
            ->setPassword($this->userPasswordHasher->hashPassword($publicUser, $password))
            ->setRoles(["ROLE_PUBLIC"]);
        $manager->persist($publicUser);

        for ($i=0; $i < 10; $i++) { 
            $userUser = new User();
            $password = $this->faker->password(2,6);
            $userUser
                ->setUsername($this->faker->userName() . "@" . $password)
                ->setPassword($this->userPasswordHasher->hashPassword($userUser, $password))
                ->setRoles(["ROLE_USER"]);
            $manager->persist($userUser);
        }
            $adminUser = new User();
            $adminUser
                ->setUsername("admin")
                ->setPassword($this->userPasswordHasher->hashPassword($adminUser, "password"))
                ->setRoles(["ROLE_ADMIN"]);
            $manager->persist($adminUser);
        
        $questions= [];
         for ($i=0; $i < 10 ; $i++) { 
            $question = new Question();
            $question->setStatement("Un tien, vaut-il vraiment mieux que 2 tu l'auras ?");
            $created = $this->faker->dateTimeBetween("-1 week", "now");
            $updated = $this->faker->dateTimeBetween($created, "now");
            $question->setCreatedAt($created)->setUpdatedAt($updated)->setStatus("on");
            $questions[] = $question;
            $manager->persist($question);
        }
        for($i=0; $i < 20; $i++ ){
            $answer = new Answer( );
            $selectedQuestion = $questions[array_rand($questions, 1)];
            $answer->setContent("Oui, ça vaut mieux de ouf")->setQuestion($selectedQuestion);
            $manager->persist($answer);
        }

        $manager->flush();

    }
}
