<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Survey;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    public function test_should_generate_link_survey()
    {
        $customer = new Customer();
        $customer['id'] = 1;
        $survey = new Survey();
        $survey['id'] = 2;

        $baseUrl = config('app.url');
        $expectedUrl = "$baseUrl/api/surveys/2/customers/1/answer";

        $url = $survey->generateLink($customer);

        $this->assertEquals($expectedUrl, $url);
    }
}
