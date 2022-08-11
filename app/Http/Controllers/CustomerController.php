<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function __construct(
        private Customer $customer
    ) {
    }

    public function store(StoreCustomerRequest $request)
    {
        $payload = $request->validated();

        $customer = $this->customer->newQuery()->create($payload);

        return CustomerResource::make($customer)
            ->response()
            ->setStatusCode(201);
    }
}
