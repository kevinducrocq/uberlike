<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository): Response
    {

        $users = $userRepository->getAllDrivers();
        $user = false;
        if ($this->getUser()) {
            $user = $this->getUser();
        }

        return $this->render('home/index.html.twig', [
            'users' => $users,
            'user' => $user,
        ]);
    }
}
