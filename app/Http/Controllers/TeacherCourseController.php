<?php namespace App\Http\Controllers;

use App\Teacher;
use App\Course;

use Illuminate\Http\Request;

class TeacherCourseController extends Controller
{
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

    public function store()
    {
        return __METHOD__;
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
                $course->title = $request->get('title');

                $course->save();

                return $this->createSuccessResponse("Course with id {$course_id} was updated.", 200);
            }
            return $this->createErrorResponse("Course with id {$course_id} does not exist.", 404);
        }
        return $this->createErrorResponse("Teacher with id {$teacher_id} does not exist.", 404);
    }

    public function destroy()
    {
        return __METHOD__;
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