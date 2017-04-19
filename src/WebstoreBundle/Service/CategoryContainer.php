<?php
namespace WebstoreBundle\Service;

use Doctrine\ORM\EntityManager;

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
        $repo = $this->em->getRepository('WebstoreBundle:Category');



        $categories = $repo->createQueryBuilder('c')
            ->select("c")
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();

        $this->categories = $categories;

        return $this->categories;
    }
}