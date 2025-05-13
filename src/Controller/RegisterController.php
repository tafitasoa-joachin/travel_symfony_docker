<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request); // Merci d'ecouter la request pour aller plus loin, il ecoute l'objet request
        // Si le formulaire est valide et soumis alors:
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_ADMIN']);
            $entityManager->persist($user); // fuser le donnée en cas du création du nouvelle objet(notamment user, produit) 
            $entityManager->flush(); // enregistrer dans la base de donnée

            $this->addFlash(
                type: 'success',
                message: 'Votre compte est correctement créé, veuillez vous connecter'
            );
            return $this->redirectToRoute('app_login'); // rediriger vers la page login
        }

        // Envoyer du message

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}
