<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\Customer;
use App\Models\Survey;
use App\Notifications\NotifyCustomerNewSurvey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct(
        private Survey $survey
    ) {
    }

    public function store(StoreSurveyRequest $request)
    {
        $payload = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'question' => $request->input('question'),
            'question_type' => $request->input('question_type', 'likert'),
        ];

        $survey = $this->survey->newQuery()->create($payload);

        $survey->customers()->sync($request->input('customers'));

        // notify with job ?
        $survey->customers->each->notify(new NotifyCustomerNewSurvey($survey));

        return SurveyResource::make($survey)
            ->response()
            ->setStatusCode(201);
    }

    public function answer(Survey $survey, Customer $customer, Request $request)
    {
        $customer->surveys()->updateExistingPivot($survey->id, [
            'answer' => $request->input('answer')
        ]);

        return response()->view('feedback', [
            'customer' => $customer,
            'survey' => $survey,
        ]);
    }

    public function result(Survey $survey)
    {
        $countCustomers = $survey->customers()->count();
        $totalPoints = $survey
            ->customers()
            ->withPivot('answer')
            ->wherePivotNotNull('customer_survey.answer')
            ->get()
            ->sum('pivot.answer');
        $countAnswers = $survey
            ->customers()
            ->wherePivotNotNull('customer_survey.answer')
            ->count();

        return [
            'total_customer' => $countCustomers,
            'total_answers' => $countAnswers,
            'total_points' => $totalPoints,
            'result' => $totalPoints / $countAnswers
        ];
    }
}
