<?php

namespace App\Controller;

use App\Service\CommissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommissionController extends AbstractController
{
    public function __construct(private CommissionService $commissionService)
    {}

    #[Route('/commission', methods: ['GET'], name: 'app_commission')]
    public function index(): Response
    {
        return $this->commissionService->getCommissions();
    }
}
