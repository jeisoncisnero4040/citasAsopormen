<?php

namespace App\Files\Domain;

use App\Files\Domain\SubRangeIndex;
use App\Domain\Code;

class PdfSplitterIndexs {
    public function __construct(
        private SubRangeIndex $subRangeIndexs,
        private ?Code $referenceCode = null
    )
    {}
    public function getSubRangeIndexs():SubRangeIndex{
        return $this->subRangeIndexs;
    }
    public function getReferenceCode():?Code{
        return $this->referenceCode;
    }
}
