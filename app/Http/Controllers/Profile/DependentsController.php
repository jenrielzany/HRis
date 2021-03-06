<?php namespace HRis\Http\Controllers\Profile;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Exception;
use HRis\Eloquent\Dependent;
use HRis\Eloquent\Employee;
use HRis\Http\Controllers\Controller;
use HRis\Http\Requests\Profile\DependentsRequest;
use Illuminate\Support\Facades\Redirect;

/**
 * @Middleware("auth")
 */
class DependentsController extends Controller {

    /**
     * @var Dependent
     */
    protected $dependent;

    /**
     * @var Employee
     */
    protected $employee;

    /**
     * @param Sentry $auth
     * @param Employee $employee
     * @param Dependent $dependent
     */
    public function __construct(Sentry $auth, Employee $employee, Dependent $dependent)
    {
        parent::__construct($auth);

        $this->employee = $employee;
        $this->dependent = $dependent;
    }

    /**
     * Show the Profile - Dependents.
     *
     * @Get("profile/dependents")
     * @Get("pim/employee-list/{id}/dependents")
     *
     * @param DependentsRequest $request
     * @param null $employee_id
     *
     * @return \Illuminate\View\View
     */
    public function index(DependentsRequest $request, $employee_id = null)
    {
        $employee = $this->employee->getEmployeeById($employee_id, $this->loggedUser->id);

        if ( ! $employee)
        {
            return Response::make(View::make('errors.404'), 404);
        }

        $this->data['employee'] = $employee;

        $this->data['pim'] = $request->is('*pim/*') ? : false;
        $this->data['pageTitle'] = $this->data['pim'] ? 'Employee Dependents' : 'My Dependents';

        return $this->template('pages.profile.dependents.view');
    }

    /**
     * Save the Profile - Dependents.
     *
     * @Post("profile/dependents")
     * @Post("pim/employee-list/{id}/dependents")
     *
     * @param DependentsRequest $request
     */
    public function store(DependentsRequest $request)
    {
        try
        {
            $this->dependent->create($request->all());
        } catch (Exception $e)
        {
            return Redirect::to($request->path())->with('danger', UNABLE_ADD_MESSAGE);
        }

        return Redirect::to($request->path())->with('success', SUCCESS_ADD_MESSAGE);
    }

    /**
     * Update the Profile - Dependents.
     *
     * @Patch("profile/dependents")
     * @Patch("pim/employee-list/{id}/dependents")
     *
     * @param DependentsRequest $request
     */
    public function update(DependentsRequest $request)
    {
        $dependent = $this->dependent->whereId($request->get('dependent_id'))->first();

        if ( ! $dependent)
        {
            return Redirect::to($request->path())->with('danger', 'Unable to retrieve record from database.');
        }

        try
        {
            $dependent->update($request->all());

        } catch (Exception $e)
        {
            return Redirect::to($request->path())->with('danger', 'Unable to update record.');
        }

        return Redirect::to($request->path())->with('success', 'Record successfully updated.');
    }

    /**
     * Delete the profile dependent.
     *
     * @Delete("ajax/profile/dependents")
     * @Delete("ajax/pim/employee-list/{id}/dependents")
     * @param DependentsRequest $request
     */
    public function deleteDependent(DependentsRequest $request)
    {
        if ($request->ajax())
        {
            $dependentId = $request->get('id');

            try
            {
                $this->dependent->whereId($dependentId)->delete();

                print('success');

            } catch (Exception $e)
            {
                print('failed');
            }
        }
    }

    /**
     * Get the profile dependent.
     *
     * @Get("ajax/profile/dependents")
     * @Get("ajax/pim/employee-list/{id}/dependents")
     *
     * @param DependentsRequest $request
     */
    public function getDependent(DependentsRequest $request)
    {
        if ($request->ajax())
        {
            $dependentId = $request->get('id');

            try
            {
                $dependent = $this->dependent->whereId($dependentId)->first();

                print(json_encode($dependent));

            } catch (Exception $e)
            {
                print('failed');
            }

        }
    }
}
