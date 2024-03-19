<?php

namespace App\Controller;

use App\Entity\Report;
use App\Service\ReportService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    #[Route(path: '/api/v1/reports', name: 'getReport', methods: 'POST')]
    public function get(Request $request): Response
    {
        try {
            $report = $this->reportService->getReport(
                $request->getPayload()->get(Report::TYPE),
                $request->getPayload()->get(Report::DATE_START),
                $request->getPayload()->get(Report::DATE_END)
            );
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $code = $exception->getCode();
        }

        return new JsonResponse(
            [
                'report' => $report ?? [],
                'success' => (bool)($report ?? false),
                'error' => $error ?? null
            ],
            $code ?? Response::HTTP_OK
        );
    }
}
