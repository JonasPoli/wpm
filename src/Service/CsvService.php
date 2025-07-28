<?php
namespace App\Service;

use App\Entity\BannerItem;
use App\Entity\BannerItemText;
use App\Entity\ErrorLog;
use App\Entity\FileUpload;
use App\Repository\ErrorLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class CsvService
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {}

    public function readFile($csvFile,$delimiter = ',')
    {
        $rows = [];
        if (($handle = fopen($csvFile, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;

    }

    public function checkIfTheHeadersMatch($csvRows, FileUpload $fileUpload){
        $math = true;
        foreach ($fileUpload->getStructure()->getBannerTexts() as $index => $bannerText) {
            if ($bannerText->getTitle() !== $csvRows[0][$index]){
                $math = false;
            }
        }
        return $math;
    }

    public function importFile($csvRows, FileUpload $fileUpload)
    {

        foreach ($csvRows as $index1 => $csvRow) {
            if ($index1 == 0){
                continue;
            }
            $bannerItem = new BannerItem();
            $bannerItem->setStructure($fileUpload->getStructure());
            $this->em->persist($bannerItem);
            $this->em->flush();

            foreach ($bannerItem->getStructure()->getBannerTexts() as $index2 => $bannerText) {

                $bannerItemText = new BannerItemText();
                $bannerItemText->setBannerText($bannerText);
                $bannerItemText->setContent($csvRow[$index2]);
                $bannerItemText->setBannerItem($bannerItem);
                $bannerItemText->setFileUpload($fileUpload);

                $this->em->persist($bannerItemText);
                $this->em->flush();
            }
        }

    }
}