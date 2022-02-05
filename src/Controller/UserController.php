<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{
    #[Route('/user/profile', name: 'user_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('user/index.html.twig', ['user' => $user]);
    }

    #[Route('/user/profile/edit', name: 'user_profile_edit')]
    public function editProfile(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $originalName = pathInfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $sluggerName = $slugger->slug($originalName);
                $newName = $sluggerName . '-' . uniqId() . '.' . $avatar->guessExtension();

                try {
                    $avatar->move($this->getParameter('upload_avatars'), $newName);
                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                $user->setAvatar($newName);
            }

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
