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
    #[Route('/user/profile/', name: 'profile_user')]
    public function index(): Response
    {

        return $this->render('user/user.html.twig', ['user' => $user]);
    }

    #[Route('/user/profile/edit', name: 'profile_user_edit')]
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

                $latlong = $this->geocode($form->get('address')->getData() . ', ' . $form->get('zip')->getData() . ' ' . $form->get('city')->getData());

                $user->setLatitude($latlong['lat']);
                $user->setLongitude($latlong['lng']);
                $user->setAvatar($newName);
            }
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('profile_user');
        }

        return $this->render('user/profile_user_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function geocode($address)
    {
        $address = urlencode($address);

        $url = 'https://api.jawg.io/places/v1/search?access-token=jJl6l1bnjX4xb0RUvALO9oiYRw1ezoEIF9vhMQMKzWDmuqxTih8FwMRJelmmqWtL&text=' . $address . '&layers=venue,address';

        $resp_json = file_get_contents($url);

        $result = json_decode($resp_json);

        $coordinates = [
            'lat' => $result->features[0]->geometry->coordinates[0],
            'lng' => $result->features[0]->geometry->coordinates[1],
        ];

        return $coordinates;
    }

    /**
     * 
     *
     */
    #[Route('/driver/profile', name: 'profile_driver')]
    public function profileDriver()
    {
        return $this->render('user/driver.html.twig');
    }
}
