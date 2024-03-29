<?php namespace App\Http\Controllers;

use App\Teacher;
use App\Course;

use Illuminate\Http\Request;

class TeacherCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('oauth', ['except' => ['index']]);
    }

    public function index($teacher_id)
    {
        $teacher = Teacher::find($teacher_id);

        if($teacher)
        {
            $courses = $teacher->courses;

            return $this->createSuccessResponse($courses, 200);
        }
        return $this->createErrorResponse("The teacher with id {$teacher_id} does not exist.", 404);
    }

    public function store(Request $request, $teacher_id)
    {
        $teacher = Teacher::find($teacher_id);

        if($teacher)
        {
            $this->validateRequest($request);

            $course = Course::create
            (
                [
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'value' => $request->get('value'),
                    'teacher_id' => $teacher->id
                ]
            );

            return $this->createSuccessResponse("The course with id {$course->id} has been created and associated with the teacher with id {$teacher->id}.", 201);
        }

        return $this->createErrorResponse("The teacher with id {$teacher_id} does not exist.", 404);


    }

    public function update(Request $request, $teacher_id, $course_id)
    {
        $teacher = Teacher::find($teacher_id);

		if($teacher)
		{
            $course = Course::find($course_id);
            
			if($course)
			{
                $this->validateRequest($request);
                
				$course->title = $request->get('title');
				$course->description = $request->get('description');
				$course->value = $request->get('value');
                $course->teacher_id = $teacher_id;
                
                $course->save();
                
				return $this->createSuccessResponse("The course with id {$course_id} was updated.", 200);
			}
			return $this->createErrorResponse("Course with the id {$course_id} does not exist.", 404);
		}
		return $this->createErrorResponse("Teacher with the id {$teacher_id} does not exist", 404);
    }

    public function destroy($teacher_id, $course_id)
    {
        $teacher = Teacher::find($teacher_id);

		if($teacher)
		{
            $course = Course::find($course_id);
            
			if($course)
			{
                if($teacher->courses()->find($course_id))
                {
                    // without params passed to detach, you remove all the students
                    $course->students()->detach();

                    $course->delete();

                    return $this->createSuccessResponse("The course with id {$course_id} was removed", 200);
                }
                
				return $this->createSuccessResponse("The course with id {$course_id} is not associated with the teacher with id {$teacher_id}.", 409);
			}
			return $this->createErrorResponse("Course with the id {$course_id} does not exist.", 404);
		}
		return $this->createErrorResponse("Teacher with the id {$teacher_id} does not exist", 404);
    }

    function validateRequest($request)
    {
        $rules = 
        [
            'title' => 'required',
            'description' => 'required',
            'value' => 'required'
        ];

        $this->validate($request, $rules);
    }
}