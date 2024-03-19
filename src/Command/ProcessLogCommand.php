<?php

namespace App\Command;

use App\Entity\FileReadHistory;
use App\Entity\Log;
use App\Repository\FileReadHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:process-log',
    description: 'Add a short description for your command',
    hidden: false,
)]
class ProcessLogCommand extends Command
{
    private Serializer $serializer;
    private SymfonyStyle $logger;

    public function __construct(
        private readonly ParameterBagInterface     $params,
        private readonly EntityManagerInterface    $entityManager,
        private readonly FileReadHistoryRepository $fileReadHistoryRepository
    )
    {
        $this->serializer = new Serializer([new ObjectNormalizer(), new DateTimeNormalizer()], [new JsonEncoder()]);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Process log file')
            ->addArgument('chunkSize', InputArgument::OPTIONAL, 'Chunk size in bytes', 200)
            ->addArgument('from', InputArgument::OPTIONAL, 'Starting file pointer', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger = new SymfonyStyle($input, $output);
        $this->logger->note('Initialize ProcessLogCommand');

        $logFilePath = $this->params->get('kernel.project_dir') . '/var/log/logs.log';
        $chunkSize = $input->getArgument('chunkSize');
        $from = $input->getArgument('from');
        $handle = fopen($logFilePath, 'r');
        $filesize = filesize($logFilePath);

        if ($from === 0) {
            $this->logger->note("initial pointer is not set, try getting it from database");
            $latestHistory = $this->fileReadHistoryRepository->findLatest();
            if ($latestHistory !== null) {
                $from = $latestHistory->getReadTill();
            }
        }

        $this->logger->note(
            sprintf(
                'Start Processing logs : %s, from: %s, chunkSize: %s, total size: %s',
                $logFilePath,
                $from,
                $chunkSize,
                $filesize
            )
        );

        if ($handle) {
            while (!feof($handle)) {

                // Move file pointer to the specified position
                $this->logger->note(sprintf('seeking file upto (%s) and reading (%s) bytes', $from, $chunkSize));
                fseek($handle, $from);
                // Read the chunk of specified size
                $chunk = fread($handle, $chunkSize);
                // Process the chunk here
                $last_read_ptr = $this->processChunk($chunk);
                $fileReadHistory = new FileReadHistory();
                $fileReadHistory->setReadFrom($from);
                $fileReadHistory->setReadTill($from + $last_read_ptr);
                $fileReadHistory->setTimestamp(new \DateTime());
                $this->entityManager->persist($fileReadHistory);
                $this->entityManager->flush();
                $from += $last_read_ptr;
            }
            fclose($handle);
            $this->entityManager->flush(); // Flush any remaining entities
        }
        return Command::SUCCESS;
    }

    private function processChunk(string $chunk): int
    {
        // searching the end of last full text line (or get remaining chunk)
        if (!($read_till_ptr = strrpos($chunk, "\n"))) {
            $read_till_ptr = mb_strlen($chunk);
        }

        // dd($chunk, $last_line_ptr);
        $buffer = mb_substr($chunk, 0, $read_till_ptr - 1);
        $lines = explode("\n", $buffer);

        $this->logger->note(sprintf('Number of lines in this chunk : %s', count($lines)));

        foreach ($lines as $line) {
            // Skip empty lines
            if ($line === '') {
                continue;
            }
            $logData = $this->parseLogLine($line);
            $log = $this->serializer->deserialize(json_encode($logData), Log::class, 'json');
            $this->entityManager->persist($log);

            // Save state into the database after processing each chunk
            $this->entityManager->flush();
            $this->entityManager->clear(); // Clear the entity manager to prevent memory leaks
        }

        return $read_till_ptr;
    }

    private function parseLogLine($line): array
    {
        // Removing any impurities \r or trailing white spaces
        $line = trim($line);
        // Define the regex pattern to match the log line
        $pattern = '/^(.*?) - - \[(.*?)\] "(.*?)" (\d+)$/';

        // Perform the regex match
        if (preg_match($pattern, $line, $matches)) {
            // Extract data from the matched groups
            $serviceName = $matches[1];
            $timestamp = $matches[2];
            $httpMethodAndEndpoint = explode(' ', $matches[3]);
            $httpMethod = $httpMethodAndEndpoint[0];
            $endpoint = $httpMethodAndEndpoint[1];
            $rawData = $matches[3];
            $statusCode = intval($matches[4]);

            // Return the extracted data as an associative array
            return [
                'serviceName' => $serviceName,
                'timestamp' => $timestamp,
                'httpMethod' => $httpMethod,
                'endpoint' => $endpoint,
                'statusCode' => $statusCode,
                'rawData' => $rawData
            ];
        } else {
            // Handle irregular log line or error
            // For simplicity, add inside rawData to filter out later
            return [
                '$rawData' => $line
            ];
        }
    }
}
