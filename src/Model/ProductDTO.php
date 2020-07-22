<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    public $sku;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    public $description;

    /**
     * @Assert\Type(type="float")
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public $normalPrice;

    /**
     * @Assert\Type(type="float")
     * @Assert\LessThan(propertyPath="normalPrice")
     * @Assert\Positive()
     */
    public $specialPrice;
}
