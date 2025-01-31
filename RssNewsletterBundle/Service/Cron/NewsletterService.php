<?php

namespace MauticPlugin\RssNewsletterBundle\Service\Cron;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MauticPlugin\RssNewsletterBundle\Helper\EmailHelper;
use MauticPlugin\RssNewsletterBundle\Helper\UserHelper;
use MauticPlugin\RssNewsletterBundle\Helper\RssHelper;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class NewsletterService
{
    private $logger;
    private $userHelper;
    private $rssHelper;
    private $emailHelper;

    public function __construct( LoggerInterface $logger, UserHelper $userHelper, RssHelper $rssHelper, EmailHelper $emailHelper)
    {
        $this->logger = $logger;
        $this->userHelper = $userHelper;
        $this->rssHelper = $rssHelper;
        $this->emailHelper = $emailHelper;
    }

    /**
     * Dispatch the NewsletterProcessEvent for eligible users
     */
    public function execute(int $limit = 1000) // Process 50 users per batch and Starting offset is 0
    {
        $rssUrl = "";
        $sentContent = "";
        $this->logger->info('Starting newsletter processing...');

        $events = $this->rssHelper->fetchFeed($rssUrl);
        // Fetch users eligible for the newsletter in the current batch
        $users = $this->userHelper->getEligibleUsers((int)$limit);

        foreach ($users as $user) {

            // Split the user's category into an array of categories
            $userCategories = array_map('trim', explode('|', $user['category'])); // Trim to avoid whitespace issues

            // Filter RSS events that match the user's categories
            $matchedEvents = array_filter($events, function ($event) use ($userCategories) {
                return in_array($event['category'], $userCategories);
            });

            // If there are matching events, prepare and send the email
            if (!empty($matchedEvents)) {
                // Initialize email content
                $content = $this->generateEmailContent($user, $matchedEvents);
                $sentContent .= $content;
                // Send the email to the user
                $this->emailHelper->sendCustomEmail(
                    $user['email'],
                    'Your Personalized Events',
                    $content
                );
                // Update user's last_sent date
                $this->userHelper->updateLastSentDate((int)$user['id']);
            }
        }

        $this->logger->info('Newsletter processing completed.');
        if (empty($users)) {
            return "No eligible users to send.";
        }
        return $sentContent;
    }

    /**
     * Run the newsletter process in batches
     * @return string
     */
    private function generateEmailContent($user, array $events): string
    {
        // Initialize email content
        $content = "\n\n" . "Hello " . htmlspecialchars($user['fullname']) . ",\n\n";
        $content .= "We found the following events related to your categories:\n\n";
        foreach ($events as $event) {
            $content .= "Event: " . htmlspecialchars($event['title']) . "\n";
            $content .= "Category: " . htmlspecialchars($event['category']) . "\n";
            $content .= "Description: " . htmlspecialchars($event['description']) . "\n";
            $content .= "Link: " . htmlspecialchars($event['link']) . "\n\n";
        }

        $content .= "Thank you,\nYour Newsletter Team";

        return $content;
    }
}
