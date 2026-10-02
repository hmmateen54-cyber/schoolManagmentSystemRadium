<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Welcome')
                ->where('canLogin', true)
                ->where('canRegister', true));
    }

    public function test_student_details_route_is_available_for_policy_authorization(): void
    {
        $this->assertTrue(Route::has('students.show'));
        $this->assertFalse(Route::has('teachers.show'));
    }
}
