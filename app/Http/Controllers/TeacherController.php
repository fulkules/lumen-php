<?php namespace App\Http\Controllers;

use App\Teacher;

use Illuminate\Http\Request;


class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('oauth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $teachers = Teacher::all();

        return $this->createSuccessResponse($teachers, 200);
    }

    public function show($id)
    {
        $teacher = Teacher::find($id);

        if($teacher)
        {
            return $this->createSuccessResponse($teacher, 200);
        }

        return $this->createErrorResponse("The teacher with id {$id} does not exist.", 404);
    }

    public function store(Request $request)
    {
        $rules = 
        [
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'profession' => 'required|in:engineering,math,physics'
        ];

        $this->validate($request, $rules);

        $teacher = Teacher::create($request->all());

        return $this->createSuccessResponse("The teacher with id {$teacher->id} has been created.", 201);
    }

    public function update(Request $request, $teacher_id)
    {
        $teacher = Teacher::find($teacher_id);

        if($teacher)
        {
            $this->validateRequest($request);

            $teacher->name = $request->get('name');
            $teacher->phone = $request->get('phone');
            $teacher->address = $request->get('address');
            $teacher->profession = $request->get('profession');

            $teacher->save();

            return $this->createSuccessResponse("The teacher$teacher with id {$teacher->id} has been updated.", 200);
        }

        return $this->createErrorResponse("The student with the specified id does not exist.", 404);
    }

    public function destroy($teacher_id)
    {
        $teacher = Teacher::find($teacher_id);

        if($teacher)
        {
            $courses = $teacher->courses;

            if(sizeof($courses) > 0)
            {
                return $this->createErrorResponse("You can't remove a teacher with active courses. Please remove the courses first.", 409);
            }

            $teacher->delete();

            return $this->createSuccessResponse("The teacher with id {$teacher_id} has been removed.", 200);
        }

        return $this->createErrorResponse('The teacher with the specified id does not exist.', 404);
    }

    function validateRequest($request)
    {
        $rules = 
        [
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'profession' => 'required|in:engineering,math,physics'
        ];

        $this->validate($request, $rules);
    }
}