<?php

namespace App\Interfaces;

use App\Dtos\BasicEvoDto;
use App\Dtos\PsicoEvoDto;
use App\Models\AppoimentInfoModel;
use App\Models\NumEvoModel;
use App\Models\ProcediproModel;

interface Evolucionable{
    public static function evoInDtoToEVO(BasicEvoDto $evoDto,
                                         AppoimentInfoModel $appoInfo,
                                         ProcediproModel $procedipro,
                                         string $now):BasicEvoDto;

    public static function evoPsicoEvoInDtoToEvo(PsicoEvoDto $evoDto,
                                        AppoimentInfoModel $appoInfo,
                                        ProcediproModel $procedipro,
                                        NumEvoModel $numEvoModel,
                                        string $now):PsicoEvoDto;

}