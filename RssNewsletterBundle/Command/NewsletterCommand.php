<?php

namespace MauticPlugin\RssNewsletterBundle\Command;

use MauticPlugin\RssNewsletterBundle\Service\Cron\NewsletterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewsletterCommand extends Command
{
    private $newsletterService;

    public function __construct(NewsletterService $newsletterService)
    {
        parent::__construct();
        $this->newsletterService = $newsletterService;
    }

    protected function configure(): void
    {
        $this
            ->setName('newsletter:process')
            ->setDescription('Dispatches newsletters from RSS feeds for subscribed users.')
            ->addOption('limit', 1000, InputOption::VALUE_REQUIRED, 'The number of items to process (e.g., 1000)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        try {
            $limit = $input->getOption('limit');
            $output->writeln('<info>Starting newsletter processing...</info>');
            $output->writeln(sprintf('Using RSS feed: %s', "Temporary"));

            $result = $this->newsletterService->execute($limit);

            $output->writeln($result);
            $output->writeln('<info>Newsletter processing completed successfully.</info>');

            $output->writeln($this->getProcessTime($startTime));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>An error occurred: %s</error>', $e->getMessage()));
            return Command::FAILURE;        }
    }

    private function getProcessTime($startTime)
    {
        $endTime = microtime(true);
        // Calculate the total execution time in seconds
        $executionTime = $endTime - $startTime;

        // Convert the time into hours, minutes, and seconds
        $hours = floor($executionTime / 3600);
        $minutes = floor(($executionTime % 3600) / 60);
        $seconds = round($executionTime % 60);
        // Display the process time
        // return sprintf(
        //     '<info>Total process time: %02d:%02d:%02d (hh:mm:ss)</info>',
        //     $hours,
        //     $minutes,
        //     $seconds
        // );
        return sprintf('<info>Total process time: %05d </info>', $executionTime);
    }
}
