<?php

namespace App\Repository;

use App\Entity\CommemorativeDates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommemorativeDates>
 */
class CommemorativeDatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommemorativeDates::class);
    }


    /**
     * Find commemorative dates around the current date within a range of 4 days before and after.
     *
     * @return CommemorativeDates[]
     */
    public function findCommemorativeDatesAroundToday($today = new \DateTime()): array
    {

        $startDate = (clone $today)->modify('-4 days');
        $endDate = (clone $today)->modify('+4 days');

        return $this->findCommemorativeDatesAroundInterval($startDate,$endDate);
    }


    /**
     * Find commemorative dates around the current date within a range of 4 days before and after.
     *
     * @return CommemorativeDates[]
     */
    public function findCommemorativeDatesAroundInterval(\DateTime $startDate, \DateTime $endDate): array
    {

//        $qb = $this->createQueryBuilder('c');
//
//        // Condition for dates without specific year
//        $qb->orWhere($qb->expr()->andX(
//            $qb->expr()->isNull('c.year'),
//            $qb->expr()->isNotNull('c.month'),
//            $qb->expr()->isNotNull('c.day'),
//            $qb->expr()->orX(
//                $qb->expr()->andX(
//                    $qb->expr()->eq('c.month', $startDate->format('m')),
//                    $qb->expr()->gte('c.day', $startDate->format('d'))
//                ),
//                $qb->expr()->andX(
//                    $qb->expr()->eq('c.month', $endDate->format('m')),
//                    $qb->expr()->lte('c.day', $endDate->format('d'))
//                ),
//                $qb->expr()->andX(
//                    $qb->expr()->gt('c.month', $startDate->format('m')),
//                    $qb->expr()->lt('c.month', $endDate->format('m'))
//                )
//            )
//        ));
//
//
//        $qb->orderBy('c.day', 'ASC');
//        $qb->orderBy('c.month', 'ASC');
//
//
//        $array1 = $qb->getQuery()->getResult();

        //dump($startDate,$endDate);
        $array1 = $this->findDatesWithinRange($startDate,$endDate);
        $array2 = $this->findDatesWithinRangeWithYear($startDate,$endDate);
        //dd($array1,$array2);
        return $this->concatenateAndSortArrays($array1,$array2);
    }


    public function concatenateAndSortArrays(array $array1, array $array2): array
    {
        // Concatenate arrays
        $combinedArray = array_merge($array1, $array2);

        // Sort combined array by month and day
        usort($combinedArray, function (CommemorativeDates $a, CommemorativeDates $b) {
            if ($a->getMonth() === $b->getMonth()) {
                return $a->getDay() <=> $b->getDay();
            }
            return $a->getMonth() <=> $b->getMonth();
        });

        return $combinedArray;
    }




    /**
     * Generate an array of dates between startDate and endDate (inclusive).
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return string[] Array of dates in 'Y-m-d' format.
     */
    private function generateDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        $startDate->modify('-4 day');
        $endDate->modify('+3 day');

        $dates = [];
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    /**
     * Find commemorative dates with specific year within a specific date range and compare with date array.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return CommemorativeDates[]
     */
    public function findDatesWithinRangeWithYear($startDate = new \DateTime(),$endDate = new \DateTime()): array
    {

        // Generate date range array
        $dateRange = $this->generateDateRange($startDate, $endDate);

        // Find all records with year defined
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->isNotNull('c.year'));

        $records = $qb->getQuery()->getResult();

        // Filter records to include only those within the date range
        $filteredRecords = array_filter($records, function ($record) use ($dateRange) {
            $recordDate = sprintf('%04d-%02d-%02d', $record->getYear(), $record->getMonth(), $record->getDay());
            return in_array($recordDate, $dateRange, true);
        });
        return $filteredRecords;
    }

    public function findDatesWithinRange($startDate = new \DateTime(),$endDate = new \DateTime()): array
    {

        // Generate date range array
        $dateRange = $this->generateDateRange($startDate, $endDate);
        // Find all records with year defined
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->isNull('c.year'));

        $records = $qb->getQuery()->getResult();

        // Filter records to include only those within the date range
        $filteredRecords = [];
        foreach ($dateRange as $date) {
            $newDateFromDateRange = new \DateTime($date);
            foreach ($records as $record) {
                //dump($record->getMonth().'-'.$record->getDay().'  ==  '. $newDateFromDateRange->format('m-d'));
                if ($this->formatNumberWithZero($record->getMonth()).'-'.$this->formatNumberWithZero($record->getDay())  == $newDateFromDateRange->format('m-d')){
                    $filteredRecords[] = $record;
                }
            }
        }
        return $filteredRecords;
    }


    public function formatNumberWithZero($integer): string
    {
        if ($integer < 10){
            return '0'.$integer;
        }
        return $integer;
    }





    //    /**
    //     * @return CommemorativeDates[] Returns an array of CommemorativeDates objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CommemorativeDates
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
