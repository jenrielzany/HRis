<?php namespace HRis\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkExperience
 * @package HRis
 */
class WorkExperience extends Model {

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $dates = ['from_date', 'to_date'];

    /**
     * @var array
     */
    protected $fillable = ['employee_id', 'company', 'job_title', 'from_date', 'to_date', 'comment'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'work_experiences';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('HRis\Eloquent\Employee', 'id', 'employee_id');
    }

    /**
     * @param $value
     */
    public function setFromDateAttribute($value)
    {
        $this->attributes['from_date'] = Carbon::parse($value) ? : null;
    }

    /**
     * @param $value
     */
    public function setToDateAttribute($value)
    {
        $this->attributes['to_date'] = Carbon::parse($value) ? : null;
    }

}
