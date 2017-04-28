<?php
namespace WebstoreBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;
use WebstoreBundle\Entity\Product;

class UploadPicture
{
    protected $kernel;

    public function uploadPicture(Product $product, $oldPic)
    {
        /** @var UploadedFile $file */
        $file = $product->getImage();
        $currentDate = new DateTime('now');
        if ($file) {
            $path = '/../web/images/products/';
            $filename = md5($product->getName() . '' . $product->getAddedOn()->format('Y-m-d H:i:s') .
                $product->getOwner()->getFirstName()

                . $currentDate->format('s'));
            $file->move(
                $this->kernel->getRootDir() . $path,
                $filename.'.png');
            $product->setImage('images/products/' . $filename . '.png');
        } else {
            $product->setImage($oldPic);
        }
    }

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

}