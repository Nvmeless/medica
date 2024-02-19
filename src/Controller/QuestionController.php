<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/api/question')]
    public function getAllQuestions(QuestionRepository $repository, SerializerInterface $serializer){
        $questions = $repository->findAll();
        $jsonQuestions = $serializer->serialize($questions, 'json',['groups' => "getAllQuestion"]);
        return new JsonResponse($jsonQuestions, 200, [], true);
    }
}
