<?php

namespace App\Http\Controllers;

use App\Http\Resources\LojaBannerResource;
use App\Services\LojaBannerService;

class LojaBannerApiController extends Controller
{
    protected $lojaBannerService;
    public function __construct(LojaBannerService $lojaBannerService){
        $this->lojaBannerService = $lojaBannerService;
    }
    
    public function listaBannerPorEmpresa($token){
        return LojaBannerResource::collection($this->lojaBannerService->listaLojaBannerPorEmpresaUuid($token));
    } 
    
}

