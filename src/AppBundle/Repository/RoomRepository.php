<?php

namespace AppBundle\Repository;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoomRepository")
 */
class RoomRepository extends \Doctrine\ORM\EntityRepository
{

    public function checkRoomAvailability($room_id, $date_start, $date_final)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();


        $qb = $em->createQueryBuilder();

        $nots = $em->createQuery("
        SELECT COUNT(b) FROM AppBundle:Reservation b
            WHERE NOT (b.dateOut   < '$date_start'
               OR
               b.dateIn > '$date_final')
            AND b.room = $room_id
               
        ")->getSingleScalarResult();

        try {

            return $nots;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getAvailableRooms($date_start, $date_final)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();


        $qb = $em->createQueryBuilder();

        $nots = $em->createQuery("
        SELECT IDENTITY(b.room) FROM AppBundle:Reservation b
            WHERE NOT (b.dateOut   < '$date_start'
               OR
               b.dateIn > '$date_final')
        ");

        $dql_query = $nots->getDQL();
        $qb->resetDQLParts();


        $query = $qb->select('r')
                    ->from('AppBundle:Room', 'r')
                    ->where($qb->expr()->notIn('r.id', $dql_query ))
                    ->getQuery()
                    ->getResult();

        try {

            return $query;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
