<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuestionController extends AbstractController
{
    #[Route('/question', name: 'app_question')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/QuestionController.php',
        ]);
    }

    #[Route('/api/question', name:"question.getAll", methods:["GET"])]
    public function getAllQuestions(QuestionRepository $repository, SerializerInterface $serializer){
        $questions = $repository->findAll();
        $jsonQuestions = $serializer->serialize($questions, 'json',['groups' => "getAllQuestion"]);
        return new JsonResponse($jsonQuestions, JsonResponse::HTTP_OK, [], true);
    }
        
    #[Route('/api/question/{idQuestion}', name:"question.get", methods:["GET"])]
    // #[Route('/api/question/{question}', name:"question.get", methods:["GET"])]
    #[ParamConverter("question", options:["id"=>"idQuestion"])]
    public function getQuestion(Question $question, SerializerInterface $serializer){
        $jsonQuestions = $serializer->serialize($question, 'json',['groups' => "getAllQuestion"]);
        return new JsonResponse($jsonQuestions, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/question', name:"question.create", methods:["POST"])]
    // #[Route('/api/question/{question}', name:"question.get", methods:["GET"])]
    public function createQuestion(Request $request, ValidatorInterface $validator,UrlGeneratorInterface $urlGenerator,  SerializerInterface $serializer, EntityManagerInterface $manager){
    //     dd($request->toArray());
    //    dd($request->getContent());
    // $question = new Question();
    $date = new \DateTime();
    $question = $serializer->deserialize($request->getContent(), Question::class,'json');
    $question
        ->setCreatedAt($date)
        ->setUpdatedAt($date)
        ->setStatus('on');
       
        $errors = $validator->validate($question);
        if($errors->count() > 0 ){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($question);
        $manager->flush();
       
        $jsonQuestions = $serializer->serialize($question, 'json',['groups' => "getAllQuestion"]);
        $location = $urlGenerator->generate("question.get", ["idQuestion" => $question->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonQuestions, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

            #[Route('/api/question/{question}', name:"question.update", methods:["PUT"])]
    // #[Route('/api/question/{question}', name:"question.get", methods:["GET"])]
    public function updateQuestion(Question $question,Request $request,  SerializerInterface $serializer, EntityManagerInterface $manager){
        $date = new \DateTime();
        
        $updatedQuestion = $serializer->deserialize($request->getContent(), 
            Question::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $question]
        );
        $updatedQuestion
        ->setUpdatedAt($date)
        ->setStatus('on');
        $manager->persist($updatedQuestion);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/question/{question}', name:"question.delete", methods:["DELETE"])]
    public function deleteQuestion(Question $question, EntityManagerInterface $manager){
        $manager->remove($question);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }




}
