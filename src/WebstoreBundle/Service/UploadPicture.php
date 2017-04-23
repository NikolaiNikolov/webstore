<?php
namespace WebstoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Kernel;
use WebstoreBundle\Entity\Product;

class UploadPicture
{
    protected $kernel;

    public function uploadPicture(Product $product)
    {
        /** @var UploadedFile $file */
        $file = $product->getImage();

        if ($file) {
            $path = '/../web/images/products/';
            $filename = md5($product->getName() . '' . $product->getAddedOn()->format('Y-m-d H:i:s'));
            $file->move(
                $this->kernel->getRootDir() . $path,
                $filename.'.png');
            $product->setImage('images/products/' . $filename . '.png');
        } else {
            $product->setImage('images/products/default.png');
        }
    }

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

}