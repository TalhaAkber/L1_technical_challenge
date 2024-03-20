<?php

namespace App\Controller;

use App\Request\CountRequest;
use App\Serializer\SerializerProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Response\CountResponse;
use App\Service\LogService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LogController extends AbstractController
{
    private readonly Serializer $serializer;
    public function __construct(
        private readonly LogService $logService,
        private readonly ValidatorInterface $validator
    ) {
        $this->serializer = SerializerProvider::getSerializer();
    }

    #[Route('/count', name: 'count_logs', methods: ['GET'])]
    #[OA\Get(
        operationId: "searchLogs",
        description: "Count all matching items in the logs.",
        summary: "Searches logs and provides aggregated count of matches",
    )]
    #[OA\Tag(name: 'analytics', description: "Analytics functions")]
    #[OA\Parameter(
        name: "serviceNames[]",
        description: "Array of service names",
        in: "query",
        schema: new OA\Schema(
            type: "array",
            items: new OA\Items(type: "string")
        ),
        explode: true
    )]
    #[OA\Parameter(
        name: "startDate",
        description: "Start date",
        in: "query",
        schema: new OA\Schema(type: "string", format: "date-time"),
        explode: true
    )]
    #[OA\Parameter(
        name: "endDate",
        description: "End date",
        in: "query",
        schema: new OA\Schema(type: "string", format: "date-time"),
        style: "form",
        explode: true
    )]
    #[OA\Parameter(
        name: "statusCode",
        description: "Filter on request status code",
        in: "query",
        schema: new OA\Schema(type: 'integer'),
        explode: true
    )]
    #[OA\Response(
        response: "200",
        description: "Count of matching results",
        content: new OA\JsonContent(ref: new Model(type: CountResponse::class))
    )]
    #[OA\Response(
        response: "400",
        description: "Bad input parameter"
    )]
    public function index(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $request = $this->serializer->deserialize(json_encode($requestData), CountRequest::class, 'json');
        $errors = $this->validator->validate($request);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return new JsonResponse($errorsString, 400);
        }

        $serviceNames = $request->getServiceNames();
        $startDate = $request->getStartDate();
        $endDate = $request->getEndDate();
        $statusCode = $request->getStatusCode();

        $countResponse = $this->logService->count((array)$serviceNames, $startDate, $endDate, $statusCode);
        return new JsonResponse($countResponse);
    }
}
