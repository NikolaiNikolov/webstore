<?php
/**
 * Created by PhpStorm.
 * User: Nikolai
 * Date: 4/29/2017
 * Time: 1:51 AM
 */

namespace WebstoreBundle\Service;


use Doctrine\ORM\EntityManager;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Entity\Promotion;

class PriceCalculator
{
    /**
     * @var EntityManager
     */
    protected $em;
    protected $promotion;
    protected $category_promotions = [];

    /**
     * PriceCalculator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param float $price
     * @return float
     */
    public function calculate(Product $product)
    {
        $category = $product->getCategory();

        if (!isset($this->category_promotions[$category->getId()])) {
            $category_prom = $this->em
                ->getRepository(Promotion::class)
                ->fetchBiggestPromotion($category);

            $this->category_promotions[$category->getId()] = $category_prom;
        }

        $promotion = $this->category_promotions[$category->getId()];

        if ($promotion === 0) {
            $this->promotion = $promotion = $this->em
                ->getRepository(Promotion::class)
                ->fetchBiggestPromotion();
        }
        
        $price = $product->getPrice();
        return $product->getPrice() - $price * ($this->promotion / 100);
    }

}