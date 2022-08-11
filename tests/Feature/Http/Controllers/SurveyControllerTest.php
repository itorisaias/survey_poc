<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Customer;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SurveyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const SURVEY_API = '/api/surveys';

    public function test_should_be_validate_required_params()
    {
        $this
            ->post(self::SURVEY_API)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'description',
                'question',
                'customers'
            ]);
    }

    public function test_should_be_validate_question_types()
    {
        $payload = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'question' => $this->faker->sentence(),
            'customers' => [],
            'question_type' => 'no_valid_type'
        ];

        $this
            ->post(self::SURVEY_API, $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'question_type'
            ]);
    }

    public function test_should_be_validate_customer_id()
    {
        $payload = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'question' => $this->faker->sentence(),
            'customers' => [1]
        ];

        $this
            ->post(self::SURVEY_API, $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'customers.0'
            ]);
    }

    public function test_should_be_create_a_survey()
    {
        $customer01 = Customer::factory()->create();
        $customer02 = Customer::factory()->create();

        $payload = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'question' => $this->faker->sentence(),
            'customers' => [
                $customer01->id,
                $customer02->id
            ],
        ];

        $this
            ->post(self::SURVEY_API, $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.name', $payload['name'])
            ->assertJsonPath('data.description', $payload['description'])
            ->assertJsonPath('data.question', $payload['question'])
            ->assertJsonPath('data.question_type', 'likert');

        $this->assertDatabaseCount('surveys', 1);
        $this->assertDatabaseCount('customer_survey', 2);
    }

    public function test_should_be_notify_customers_when_create_survey()
    {
        $customer01 = Customer::factory()->create();
        $customer02 = Customer::factory()->create();

        $payload = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'question' => $this->faker->sentence(),
            'customers' => [
                $customer01->id,
                $customer02->id,
            ],
        ];

        Notification::fake();

        $this->post(self::SURVEY_API, $payload);

        Notification::assertCount(2);
    }

    public function test_should_save_answer_customer()
    {
        $customer = Customer::factory()->create();
        $survey = Survey::factory()->create();
        $survey->customers()->sync($customer);

        $this
            ->get(self::SURVEY_API . "/$survey->id/customers/$customer->id/answer?answer=10")
            ->assertStatus(200)
            ->assertViewIs('feedback');

        $this
            ->assertDatabaseHas('customer_survey', [
                'survey_id' => $survey->id,
                'customer_id' => $customer->id,
                'answer' => 10
            ]);
    }

    public function test_should_return_result_survey()
    {
        $customer01 = Customer::factory()->create();
        $customer02 = Customer::factory()->create();
        $customer03 = Customer::factory()->create();
        $survey = Survey::factory()->create();
        $survey
            ->customers()
            ->sync([
                $customer01->id,
                $customer02->id,
                $customer03->id
            ]);

        $this->assertDatabaseCount('customer_survey', 3);

        $this->get(self::SURVEY_API . "/$survey->id/customers/$customer01->id/answer?answer=10");
        $this->get(self::SURVEY_API . "/$survey->id/customers/$customer02->id/answer?answer=2");

        $this
            ->get(self::SURVEY_API . "/$survey->id/result")
            ->assertStatus(200)
            ->assertJson([
                'total_customer' => 3,
                'total_answers' => 2,
                'total_points' => 12,
                'result' => 6
            ]);
    }
}
