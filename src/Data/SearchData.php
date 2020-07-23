<?php

namespace App\Data;

use App\Entity\Brewery;
use App\Entity\Country;
use App\Entity\Style;

class SearchData
{
    /**
     * Pour l'utilisation de paginator
     * @var int
     */
    public $page = 1;

    /**
     * 
     * @var string
     */
    public $q = '';

    /**
     * 
     * @var Style[]
     */
    public $style = [];

    /**
     * @var Country[]
     */
    public $country = [];

    /**
     * 
     * @var Brewery[]
     */
    public $brewery = [];
}