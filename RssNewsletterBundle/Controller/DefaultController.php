<?php

namespace MauticPlugin\RssNewsletterBundle\Controller;


///THIS CONTROLLER IS FOR THE TESTING PURPOSE ONLY

use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\JsonResponse;

use MauticPlugin\RssNewsletterBundle\Service\Cron\NewsletterService;
use Symfony\Component\DependencyInjection\ContainerInterface;





class DefaultController extends CommonController
{

    private $newsletterService;

    public function __construct(NewsletterService $newsletterService,ContainerInterface $container)
    {
        $this->newsletterService = $newsletterService;
        $this->setContainer($container);
    }
    public function sendAction()
    {
        try {
            $limit=100;
            $newsletterService = $this->newsletterService->execute($limit);

            return new JsonResponse(['success' => true,  'newsletter'=>$newsletterService,'message' => 'Emails sent successfully.']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
