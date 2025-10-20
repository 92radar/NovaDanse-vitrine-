<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class BackOfficeController
{
    #[Route('/admin', name: 'backoffice')]
    public function index(Request $request): Response
    {
        return new Response(
            '<html><body><h1>Welcome to the Back Office Nova Danse Symfony!</h1></body></html>'
        );
    }
}