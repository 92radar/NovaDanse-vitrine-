<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce contrôleur temporaire exécute phpinfo() directement via le tampon de sortie (Output Buffering).
 * C'est la méthode la plus fiable pour le débogage sans passer par Twig.
 */
final class DebugController extends AbstractController
{
    #[Route('/debug', name: 'app_debug')]
    public function index(): Response
    {
        // 1. Démarrer le tampon de sortie pour capturer le contenu HTML
        ob_start();
        
        // 2. Exécuter la fonction phpinfo() qui va imprimer son contenu dans le tampon
        phpinfo();
        
        // 3. Récupérer le contenu du tampon dans une variable et le nettoyer
        $phpInfoContent = ob_get_clean();
        
        // 4. Encapsuler le résultat dans un objet Response et le retourner
        return new Response($phpInfoContent);
    }
}
