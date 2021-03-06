<?php

namespace App\Omim;

use GuzzleHttp\Client;

interface OmimClientContract
{
    public function __construct($client = null);

    public function getEntry($mimNumber);

    public function search($searchData);

    public function geneSymbolIsValid($geneSymbol);

    public function getGenePhenotypes($geneSymbol);
}
