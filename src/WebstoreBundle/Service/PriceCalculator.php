<?php

namespace WebstoreBundle\Service;

use Doctrine\ORM\EntityManager;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Repository\PromotionRepository;

class PriceCalculator
{
    /** @var  PromotionManager */
    protected $manager;

    public function __construct(PromotionManager $manager) {
        $this->manager= $manager;
    }


    /**
     * @param Product $product
     *
     * @return float
     */
    public function calculate($product)
    {
        $category    = $product->getCategory();
        $category_id = $category->getId();

        $promotion = $this->manager->getGeneralPromotion();
        if($this->manager->hasCategoryPromotion($category)){
            $promotion = $this->manager->getCategoryPromotion($category);
        }

        if(isset($this->category_promotions[$category_id])){
            $promotion = $this->category_promotions[$category_id];
        }

        return $product->getPrice() - $product->getPrice() * ($promotion / 100);
    }
}
