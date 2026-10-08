<?php

namespace App\Adapters;

use Illuminate\Http\UploadedFile;
use App\Files\Concrats\Adaptable;

abstract class HttpFileAdater
{
    public abstract function adapt(UploadedFile $file): Adaptable;

}