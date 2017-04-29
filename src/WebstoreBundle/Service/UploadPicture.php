<?php

namespace WebstoreBundle\Service;

use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;
use WebstoreBundle\Entity\Product;

class UploadPicture
{
    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function uploadPicture($product, string $oldPic, string $path)
    {
        /** @var UploadedFile $file */
        $file = $product->getImage();

        $currentDate = new DateTime('now');
        if ($file) {
            $imgPath = '/../web/' . $path;

            $filename = md5( uniqid() . $currentDate->format('s'));

            $file->move(
                $this->kernel->getRootDir() . $imgPath,
                $filename . '.png');
            $product->setImage($path . $filename . '.png');
        } else {
            $product->setImage($oldPic);
        }
    }

}