<?php

namespace App\Interfaces;

use App\Dtos\BasicEvoDto;
use App\Dtos\PsicoEvoDto;
use App\Models\AppoimentInfoModel;
use App\Models\FeeModel;
use App\Models\NumEvoModel;
use App\Models\ProcediproModel;

interface Evolucionable{
    public static function evoInDtoToEVO(BasicEvoDto $evoDto,
                                         AppoimentInfoModel $appoInfo,
                                         ProcediproModel $procedipro,
                                         FeeModel $fee,
                                         string $now):BasicEvoDto;

    public static function evoPsicoEvoInDtoToEvo(PsicoEvoDto $evoDto,
                                        AppoimentInfoModel $appoInfo,
                                        ProcediproModel $procedipro,
                                        NumEvoModel $numEvoModel,
                                        FeeModel $fee,
                                        string $now):PsicoEvoDto;

}