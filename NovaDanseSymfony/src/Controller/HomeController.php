<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GoogleCalendarService;
use App\Form\ContactType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, GoogleCalendarService $calendarService, MailerInterface $mailer): Response
    {
        //  Récupération des 3 prochains événements
        $upcomingEvents = $calendarService->getUpcomingEvents(3);

        //  Création du formulaire de contact
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        //  Gestion de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //  Construction de l’e-mail
            $email = (new Email())
                ->from('viepro.db@gmail.com')           // Adresse du MAILER_DSN
                ->to('viepro.db@gmail.com')           // Destinataire réel
                ->replyTo($data['email'])                // Mail de l’utilisateur
                ->subject('Nouveau message depuis le Novadanse site web')
                ->text(
                    "Nom : {$data['name']}\n".
                    "Email : {$data['email']}\n\n".
                    "Message :\n{$data['message']}"
                );

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Merci, votre message a bien été envoyé !');
            } catch (\Exception $e) {
                // Affiche une erreur si l’envoi échoue
                $this->addFlash('error', 'Impossible d’envoyer le mail : '.$e->getMessage());
            }

            return $this->redirectToRoute('home');
        }

        //  Rendu de la vue
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'upcoming_events' => $upcomingEvents,
            'form' => $form->createView(),
        ]);
    }
}
