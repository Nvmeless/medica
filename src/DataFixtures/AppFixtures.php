<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;
    
    public function __construct(){
        $this->faker= Factory::create("fr_FR");
    }
    
    public function load(ObjectManager $manager): void
    {

        
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
            $selectedQuestion = $questions[array_rand( $questions, 1)];
            $answer->setContent("Oui, ça vaut mieux de ouf")->setQuestion($selectedQuestion);
            $manager->persist($answer);
        }

        $manager->flush();

    }
}
