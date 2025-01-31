<?php

namespace MauticPlugin\RssNewsletterBundle\Helper;

use Doctrine\DBAL\Connection;

class UserHelper
{

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    
    /**
     * Fetch users eligible for newsletters based on interval and last_sent date
     */
    public function getEligibleUsers(int $limit): array
    {
        $sql = "SELECT id, 
         COALESCE(CONCAT(firstname, ' ', lastname), 'Customer') AS fullname, 
         email, 
         nf_category AS category
         FROM leads 
         WHERE (email IS NOT NULL) 
         AND (nf_category IS NOT NULL) 
         AND (
            (nf_last_sent_date IS NULL) 
            OR 
            (nf_interval = '2W' AND nf_last_sent_date < DATE_SUB(CURDATE(), INTERVAL 2 WEEK)) 
            OR 
            (nf_interval = '1W' AND nf_last_sent_date < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) 
            OR 
            (nf_interval = '1M' AND nf_last_sent_date < DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
         )
         ORDER BY id ASC
         LIMIT :limit";
 
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("limit", (int)$limit,  \PDO::PARAM_INT);
        
        $result = $stmt->executeQuery();

        // Fetch the result
        return $result->fetchAllAssociative();
    }

 

    /**
     * Update the "last_sent" date for a user.
     *
     * @param int $userId
     */
    public function updateLastSentDate(int $leadId): void
    {

        $sql = "UPDATE leads SET nf_last_sent_date = CURDATE() WHERE id = :leadId";
    
        $this->connection->executeStatement($sql, [
            'leadId'      => $leadId           
        ]);
         
    }
}
