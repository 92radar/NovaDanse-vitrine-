<?php
// src/Service/GoogleCalendarService.php

namespace App\Service;

use Google\Client;
use Google\Service\Calendar;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class GoogleCalendarService
{
    private Calendar $calendarService;
    // Votre ID de calendrier
    private string $calendarId = 'df4b9c58ae85fd82aa315b119bfd24023c06e67322c5ce0ad8f40731594fe98d@group.calendar.google.com';
    private LoggerInterface $logger;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->logger = $logger;
        
        try {
            // 1. Initialiser le client Google
            $client = new Client();
            
            // 2. Définir le chemin vers le fichier de clé de service
            $keyFilePath = $params->get('kernel.project_dir') . '/config/secrets/nova-danse-calendar-api-7f312795c2b3.json';
            
            if (!file_exists($keyFilePath)) {
                 throw new \Exception("Le fichier de clé de service Google est introuvable à l'emplacement: " . $keyFilePath);
            }

            $client->setAuthConfig($keyFilePath);
            $client->setApplicationName('NovaDanse Event Reader');
            $client->setScopes([Calendar::CALENDAR_READONLY]);

            // 3. Initialiser le service Calendar
            $this->calendarService = new Calendar($client);

        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'initialisation du service Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les N prochains événements, à partir de maintenant.
     * @param int $maxResults Le nombre maximum d'événements à retourner.
     * @return array La liste des événements (objets Google_Service_Calendar_Event) ou un tableau vide en cas d'erreur.
     */
    public function getUpcomingEvents(int $maxResults = 3): array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        
        $options = [
            'maxResults' => $maxResults,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $now->format(\DateTime::RFC3339),
            'showDeleted' => false,
        ];

        try {
            $events = $this->calendarService->events->listEvents($this->calendarId, $options);
            return $events->getItems();
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des événements du calendrier: ' . $e->getMessage());
            return [];
        }
    }
}