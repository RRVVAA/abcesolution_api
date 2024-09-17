<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LojaConfiguracao extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'nome', 'link', 'logo', 'rua', 'numero', 
        'bairro', 'cidade', 'uf', 'cep', 'telefone', 'email',
        'link_facebook', 'link_twiter', 'link_instagram', 'frete_gratis_valor',
        'mercadopago_public_key', 'mercadopago_access_token',
        'funcionamento', 'latitude', 'longitude', 'politica_privacidade', 'src_mapa', 'cor_principal',
        'tema_ecommerce', 'token', 'google_api' ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
