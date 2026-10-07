<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class School extends Model {
    protected $fillable = ['name','region','district','address','phone'];

    public function users()    { return $this->hasMany(User::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function cases()    { return $this->hasMany(RiskCase::class); }
}