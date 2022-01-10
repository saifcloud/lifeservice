<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver_document extends Model
{
    use HasFactory;
    
    const CREATED_AT ='CreatedAt';
    const UPDATED_AT ='UpdatedAt';
    
    public function document_option(){
        return $this->hasOne("App\Models\Document_option","id","document_type_id");
    }


    
}