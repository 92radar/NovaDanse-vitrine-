<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

// Utilisation des classes FQCN (Fully Qualified Class Name) directement dans les méthodes
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;


/**
 * Service pour interroger l'API Google Analytics Data (GA4).
 */
class GoogleAnalyticsService
{
    // Déclaration de propriété sans le type hint strict pour plus de robustesse.
    // L'autowiring de Symfony n'a pas besoin de ce type hint pour fonctionner.
    // @var BetaAnalyticsDataClient
    private $client; 
    private string $propertyId;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        // REMPLACEZ 'VOTRE_ID_PROPRIETE_GA4' PAR LE VRAI NUMÉRO DE PROPRIÉTÉ GA4 (Ex: '123456789')
        $this->propertyId = 'VOTRE_ID_PROPRIETE_GA4'; 
        
        // Chemin vers votre fichier de clé JSON Google Cloud
        $keyFilePath = $params->get('kernel.project_dir') . '/config/secrets/service-account-key.json';
        
        try {
            $this->client = new BetaAnalyticsDataClient([
                'credentials' => $keyFilePath,
            ]);
        } catch (\Exception $e) {
            $logger->error('Erreur lors de l\'initialisation du client GA4: ' . $e->getMessage());
            // En cas d'échec de l'initialisation (clé manquante, etc.), on continue sans client fonctionnel.
            // Cela empêchera l'application de crasher.
            // On initialise un objet BetaAnalyticsDataClient vide pour éviter les erreurs de type dans les méthodes.
            $this->client = new BetaAnalyticsDataClient(); 
        }
    }

    /**
     * Récupère les métriques d'audience pour une période donnée.
     * @param int $days Nombre de jours en arrière (ex: 30 pour 30 derniers jours)
     * @return array Tableau contenant les métriques d'audience.
     */
    public function getAudienceSummary(int $days = 30): array
    {
        try {
            // S'assurer que le client a été initialisé correctement
            if (!$this->propertyId || str_contains($this->propertyId, 'VOTRE_ID_PROPRIETE_GA4')) {
                 return ['error' => 'Property ID is not configured.', 'activeUsers' => 0, 'newUsers' => 0, 'engagementRate' => 0, 'days' => $days];
            }
            
            // On vérifie que $this->client est une instance fonctionnelle avant d'appeler runReport
            if (!($this->client instanceof BetaAnalyticsDataClient)) {
                 return ['error' => 'GA4 Client Initialization Failed.', 'activeUsers' => 0, 'newUsers' => 0, 'engagementRate' => 0, 'days' => $days];
            }

            $response = $this->client->runReport([
                'property' => 'properties/' . $this->propertyId,
                'dateRanges' => [
                    new DateRange([
                        'start_date' => $days . 'daysAgo',
                        'end_date' => 'today',
                    ]),
                ],
                'metrics' => [
                    new Metric(['name' => 'activeUsers']),
                    new Metric(['name' => 'newUsers']),
                    new Metric(['name' => 'engagementRate']),
                ],
            ]);
        } catch (\Throwable $e) {
             // Log l'erreur réelle de l'API (ex: permission refusée)
             error_log('GA4 API Error: ' . $e->getMessage());
             return ['error' => 'API Request Failed.', 'activeUsers' => 0, 'newUsers' => 0, 'engagementRate' => 0, 'days' => $days];
        }


        $summary = [
            'activeUsers' => 0,
            'newUsers' => 0,
            'engagementRate' => 0,
            'days' => $days,
        ];
        
        if ($response->getRows()->count() > 0) {
            $row = $response->getRows()[0];
            $metrics = $row->getMetricValues();
            
            $summary['activeUsers'] = (int) $metrics[0]->getValue();
            $summary['newUsers'] = (int) $metrics[1]->getValue();
            // Le taux d'engagement est une fraction, on le multiplie par 100 pour obtenir un pourcentage
            $summary['engagementRate'] = round((float) $metrics[2]->getValue() * 100, 2);
        }

        return $summary;
    }
    
    // Vous pouvez ajouter d'autres méthodes pour les pages principales, les sources, etc.
}
