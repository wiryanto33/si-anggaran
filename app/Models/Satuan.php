<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'parent_id',
        'aktif'
    ];

    public function parent()
    {
        return $this->belongsTo(Satuan::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Satuan::class, 'parent_id');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
    public function annualBudgets()
    {
        return $this->hasMany(AnnualBudget::class);
    }
}
