<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/', name: 'offer_list')]
    public function index(OfferRepository $offerRepository)
    {
        $offers = $offerRepository->findAll();

        return $this->render('home/home.html.twig', [
            'offers' => $offers,
        ]);
    }

    #[Route('/recherche', name: 'offer_search', methods: ['POST'])]
    public function search(Request $request, OfferRepository $offerRepository): Response
    {
        // Récupérer les données du formulaire
        $destination = $request->request->get('destination');

        // Rechercher les offres en fonction de la destination et des dates
        $qb = $offerRepository->createQueryBuilder('o')
            ->where('o.destination = :destination')
            ->setParameter('destination', $destination);

        $offers = $qb->getQuery()->getResult();

        // Retourner la vue avec les résultats
        return $this->render('home/home.html.twig', [
            'offers' => $offers,
        ]);
    }



    #[Route('/offre/{id}', name: 'offer_detail')]
    public function detail($id, OfferRepository $offerRepository)
    {
        $offer = $offerRepository->find($id);

        if (!$offer) {
            throw $this->createNotFoundException('Offre non trouvée');
        }

        return $this->render('home/offer/detail.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/offre/{id}/reserve', name: 'offer_reserve', methods: ['GET', 'POST'])]
    public function reserve($id, OfferRepository $offerRepository, Request $request, EntityManagerInterface $em): Response
    {
        $offer = $offerRepository->find($id);

        if (!$offer instanceof Offer) {
            throw $this->createNotFoundException('Offre non trouvée');
        }

        $reservation = new Reservation();
        $reservation->setOffer($offer);

        $form = $this->createForm(ReservationType::class, $reservation, [
            'action' => $this->generateUrl('offer_reserve', ['id' => $id]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Gérer la logique de réservation, par exemple, diminuer les places disponibles
                $offer->setAvailableSeats($offer->getAvailableSeats() - $reservation->getNbPersons());
                $em->persist($reservation);
                $em->flush();

                $this->addFlash('success', 'Réservation effectuée avec succès!');

                if ($request->isXmlHttpRequest()) {
                    return $this->json(['success' => true]);
                }

                return $this->redirectToRoute('offer_list');
            } else {
                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => false,
                        'form' => $this->renderView('home/offer/_reservation_form.html.twig', [
                            'form' => $form->createView(),
                            'offer' => $offer
                        ])
                    ]);
                }
            }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'form' => $this->renderView('home/offer/_reservation_form.html.twig', [
                    'form' => $form->createView(),
                    'offer' => $offer
                ])
            ]);
        }

        return $this->render('home/offer/reserve.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }
}
