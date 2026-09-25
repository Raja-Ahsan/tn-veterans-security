<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GuardTrainingForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GuardTrainingFormController extends Controller
{
    public function show(GuardTrainingForm $guardTrainingForm): View
    {
        $student = Auth::guard('student')->user();
        abort_unless(
            $guardTrainingForm->student_id === $student->id && $guardTrainingForm->isPublished(),
            404
        );

        $guardTrainingForm->load(['service']);

        return view('guard-training-forms.print', [
            'form' => $guardTrainingForm,
            'forAdmin' => false,
        ]);
    }
}
