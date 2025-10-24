<?php

namespace App\Service;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient; // Correction du namespace
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Analytics\Data\V1beta\RunReportRequest;

/**
 * Service pour interagir avec l'API Google Analytics Data (GA4).
 * Ce service nécessite que les variables d'environnement suivantes soient définies :
 * - GOOGLE_ANALYTICS_PROPERTY_ID : ID numérique de la propriété GA4 (e.g., '123456789').
 * - GOOGLE_APPLICATION_CREDENTIALS : Chemin vers le fichier JSON du compte de service.
 */
class GoogleAnalyticsService
{
    private string $propertyId;
    private ?BetaAnalyticsDataClient $client = null;

    /**
     * @param string $googleAnalyticsPropertyId L'ID de la propriété GA4 injecté via services.yaml
     * (définie dans le .env sous GOOGLE_ANALYTICS_PROPERTY_ID).
     */
    public function __construct(string $googleAnalyticsPropertyId)
    {
        $this->propertyId = $googleAnalyticsPropertyId;
        
        // La librairie Google s'authentifie automatiquement en utilisant la variable 
        // d'environnement GOOGLE_APPLICATION_CREDENTIALS.
        try {
            // Utilise la classe avec le namespace Client\
            $this->client = new BetaAnalyticsDataClient();
        } catch (\Exception $e) {
            // Loggez l'erreur pour le débogage, mais ne cassez pas l'application
            // si le service n'est pas nécessaire pour toutes les pages.
            error_log('Google Analytics Client Error: ' . $e->getMessage());
            $this->client = null;
        }
    }

    /**
     * Récupère un résumé des principales métriques d'audience pour les X derniers jours.
     * @param int $days Nombre de jours à inclure dans le rapport (ex: 30 pour 30 jours).
     * @return array Tableau associatif des métriques (e.g., ['activeUsers' => 1200, ...])
     */
    public function getAudienceSummary(int $days = 30): array
    {
        if ($this->client === null) {
            return $this->getEmptyStats();
        }

        // Définir la plage de dates
        $dateRanges = [
            new DateRange([
                'start_date' => $days . 'daysAgo',
                'end_date' => 'today',
            ]),
        ];

        // Définir les métriques clés pour le résumé
        $metrics = [
            // Nombre total d'utilisateurs actifs
            new Metric(['name' => 'activeUsers']),
            // Nombre de nouveaux utilisateurs
            new Metric(['name' => 'newUsers']),
            // Taux d'engagement moyen
            new Metric(['name' => 'engagementRate']),
        ];

        // Construire la requête de rapport
        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'date_ranges' => $dateRanges,
            'metrics' => $metrics,
        ]);

        try {
            /** @var RunReportResponse $response */
            $response = $this->client->runReport($request);
            
            // Les résultats de la métrique sont dans l'agrégat du rapport (row)
            $row = $response->getRows()->offsetGet(0);
            $metricValues = $row->getMetricValues();

            // S'assurer qu'il y a des valeurs
            if ($metricValues->count() === 3) {
                return [
                    'activeUsers' => (int)$metricValues->offsetGet(0)->getValue(),
                    'newUsers' => (int)$metricValues->offsetGet(1)->getValue(),
                    // Formater le taux d'engagement en pourcentage (arrondi à 2 décimales)
                    'engagementRate' => round((float)$metricValues->offsetGet(2)->getValue() * 100, 2),
                    'days' => $days,
                ];
            }

        } catch (\Exception $e) {
            error_log("Google Analytics API Error: " . $e->getMessage());
        }

        return $this->getEmptyStats();
    }

    /**
     * Fournit des statistiques vides en cas d'erreur ou de client non initialisé.
     */
    private function getEmptyStats(): array
    {
        return [
            'activeUsers' => 0,
            'newUsers' => 0,
            'engagementRate' => 0.0,
            'days' => 30,
        ];
    }
}
