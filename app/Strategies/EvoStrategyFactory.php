<?php

namespace App\Strategies;


use App\Models\ProcediproModel;

use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Strategies\Evo\AbaEvoStrategy;
use App\Strategies\Evo\EvoStrategy;
use App\Strategies\Evo\PsicoEvoStrategy;
use App\Strategies\Evo\RehabilitationEvoStrategy;

class EvoStrategyFactory {
    public function __construct(
        private EvoSearchStrategyFactory $searchFactory
    ) {}

    public function make(ProcediproModel $procedipro): EvoStrategy {
        if ($procedipro->formatPrintOfPsico()) {
            return new PsicoEvoStrategy($this->searchFactory);
        }

        if ($procedipro->formatPrintOfAba()) {
            return new AbaEvoStrategy($this->searchFactory);
        }
        
        if($procedipro->formatPrintOfRehabilitacion()){
            return new RehabilitationEvoStrategy($this->searchFactory);
        }


        throw new ServerErrorException("Formato de impresión no soportado", 500);
    }
}