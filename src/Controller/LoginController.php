<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted as AttributeIsGranted;

class LoginController extends AbstractController
{
    // Route pour la page de connexion
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur (email) saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affiche la vue Twig pour la page de connexion avec les erreurs et le dernier username
        return $this->render('login/index.html.twig', [
            'error' => $error, // Passe l'erreur éventuelle à la vue
            'last_username' => $lastUsername // Passe le dernier nom d'utilisateur à la vue
        ]);
    }

    // Route pour la déconnexion de l'utilisateur
    #[AttributeIsGranted('ROLE_ADMIN')]
    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // Cette méthode est interceptée par Symfony, on ne doit pas l'implémenter
        throw new \Exception(message: 'Ne pas activer manuellement la déconnexion dans ce contrôleur, configurer dans security.yaml');
    }
}
