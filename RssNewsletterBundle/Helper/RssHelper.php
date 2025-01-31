<?php

namespace MauticPlugin\RssNewsletterBundle\Helper;

class RssHelper
{
    public function fetchFeed($url)
    {
        $events = [];
        if(isset($url) && trim($url) !== ''){
            $rss = simplexml_load_file($url);
            foreach ($rss->channel->item as $item) {
                $events[] = [
                    'title' => (string) $item->title,
                    'category' => (string) $item->category,
                    'description' => (string) $item->description,
                    'link' => (string) $item->link,
                    'pubDate' => (string) $item->pubDate,
                ];
            }
        }else{
            //temporary for testing purpose, need to remove in prod
                $events[] = [
                    'title' => "title1",
                    'category' => "Art",
                    'description' => "art",
                    'link' => "www.gmail.com",
                    'pubDate' => "2025-01-22",
                ];
                $events[] = [
                        'title' => "title2",
                        'category' => "Science",
                        'description' => "travel",
                        'link' => "www.google.com",
                        'pubDate' => "2025-01-22",
                    ];
                $events[] = [
                        'title' => "title3",
                        'category' => "Friction",
                        'description' => "segment",
                        'link' => "www.hotmail.com",
                        'pubDate' => "2025-01-23",
                    ];
        }
        return $events;
    }
}
