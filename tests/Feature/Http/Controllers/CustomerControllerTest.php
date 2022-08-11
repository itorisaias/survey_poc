<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const CUSTOMER_API = '/api/customers';

    public function test_should_be_validate_required_params()
    {
        $this
            ->post(self::CUSTOMER_API)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email'
            ]);
    }

    public function test_should_be_create_a_customer()
    {
        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email()
        ];

        $this
            ->post(self::CUSTOMER_API, $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.name', $payload['name'])
            ->assertJsonPath('data.email', $payload['email']);

        $this->assertDatabaseCount('customers', 1);
    }
}
