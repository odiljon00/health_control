<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CaseAction extends Model {
    protected $fillable = ['risk_case_id','user_id','action_type','note','result'];

    public function riskCase() { return $this->belongsTo(RiskCase::class); }
    public function user()     { return $this->belongsTo(User::class); }
}