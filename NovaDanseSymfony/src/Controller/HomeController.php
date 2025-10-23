<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GoogleCalendarService; // IMPORTANT : Seulement l'instruction "use" ici.

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, GoogleCalendarService $calendarService): Response
    {
        // Appel du service pour récupérer les 3 prochains événements
        $upcomingEvents = $calendarService->getUpcomingEvents(3); 
         
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'upcoming_events' => $upcomingEvents, // Passage de la variable à Twig
        ]);
    }
}
