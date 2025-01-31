<?php
return [
    'name'        => 'Rss Newsletter bundle',
    'description' => 'Provides an interface for emailing rss newsletter.',
    'version'     => '1.0',
    'author'      => 'Dibyam Raj Ghimire',
    'routes'      => [
            'api' => [
                'rss_newsletter_run' => [
                    'path' => '/send',
                    'controller' =>'MauticPlugin\RssNewsletterBundle\Controller\DefaultController::sendAction',
                    'method'=>"GET"
                ],
            ],
        ],
    'commands' => [
            MauticPlugin\RssNewsletterBundle\Command\NewsletterCommand::class,
        ],
    'services'    => [
        'controllers' => [
        'RssNewsletterBundle.controller.default' => [
            'class'     => MauticPlugin\RssNewsletterBundle\Controller\DefaultController::class,
            'arguments' => [
                '@RssNewsletterBundle.service.newsletter_cron', // Inject the service
                '@service_container', // Inject the container
                ],
            ],
        ],
        'other'=>[
            'RssNewsletterBundle.service.newsletter_cron' => [
                'class'     => MauticPlugin\RssNewsletterBundle\Service\Cron\newsletterService::class,
                'arguments' => [
                    '@monolog.logger',
                    '@RssNewsletterBundle.user_helper',
                    '@RssNewsletterBundle.rss_helper',
                    '@RssNewsletterBundle.email_helper',
                ]
            ],

            'RssNewsletterBundle.rss_helper' => [
                'class' => MauticPlugin\RssNewsletterBundle\Helper\RssHelper::class,
            ], 
            'RssNewsletterBundle.user_helper' => [
                'class' => MauticPlugin\RssNewsletterBundle\Helper\UserHelper::class,
                'arguments' => ['@doctrine.dbal.default_connection'], 
            ],
            'RssNewsletterBundle.email_helper' => [
                'class' => MauticPlugin\RssNewsletterBundle\Helper\EmailHelper::class,
                'arguments' => ['@mautic.email.model.email'],
            ],
        ],
    ]
];