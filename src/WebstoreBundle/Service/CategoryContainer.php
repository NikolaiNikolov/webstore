<?php
namespace WebstoreBundle\Service;

use Doctrine\ORM\EntityManager;
use WebstoreBundle\Entity\Category;

class CategoryContainer
{
    private $em;

    private $categories;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCategories()
    {
        $repo = $this->em->getRepository(Category::class);

        $categories = $repo->createQueryBuilder('c')
            ->select("c")
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();

        $this->categories = $categories;

        return $this->categories;
    }
}