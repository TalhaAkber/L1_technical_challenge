<?php

namespace App\DataFixtures;

use App\Entity\Log;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LogFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Dummy log data
        $logs = [
            ["service" => "USER-SERVICE", "statusCode" => 200, "timestamp" => "2018-08-10T09:21:53", "httpMethod" => "POST", "endpoint" => "/create"],
            ["service" => "USER-SERVICE", "statusCode" => 400, "timestamp" => "2018-08-11T09:21:54", "httpMethod" => "GET", "endpoint" => "/user"],
            ["service" => "INVOICE-SERVICE", "statusCode" => 201, "timestamp" => "2018-08-12T09:21:55", "httpMethod" => "GET", "endpoint" => "/invoice"],
            ["service" => "USER-SERVICE", "statusCode" => 201, "timestamp" => "2018-08-13T09:21:56", "httpMethod" => "GET", "endpoint" => "/user"],
            ["service" => "USER-SERVICE", "statusCode" => 201, "timestamp" => "2018-08-14T09:21:57", "httpMethod" => "GET", "endpoint" => "/user"]
        ];
        foreach ($logs as $logData) {
            $log = new Log();
            $log->setServiceName($logData['service']);
            $log->setTimestamp($logData['timestamp']);
            $log->setHttpMethod($logData['httpMethod']);
            $log->setEndpoint($logData['endpoint']);
            $log->setStatusCode($logData['statusCode']);
            $manager->persist($log);
        }

        $manager->flush();
    }
}
