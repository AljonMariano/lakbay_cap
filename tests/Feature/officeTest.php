<?php

namespace Tests\Feature;

use App\Models\Offices;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

class officeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_add_office(): void
    {
        $formData = [
        	
         'off_acr'	=>  'ABC',
         'off_name'	 =>   'asdasd',
         'off_head'   =>  'mark',
        ];
        $officeData = [
            'off_acr'    =>  'ABC',
            'off_name'     =>   'asdasd',
            'off_head'   =>  'mark',
        ];
        $user = User::factory()->create();
        $this->assertCount(0, $user->tokens);
        $this->actingAs($user);
        $this->post('/insert-office', $formData);

        // this will check if it is inserted in the database
        $response = $this->assertDatabaseHas('offices', $officeData);
    }

    public function test_edit_office(): void
    {

        $sg = Offices::where([["off_acr", "ABC"], ["off_name", "asdasd"], ["off_head", "mark"]])->first();

        $formData = [

            'off_acr'    =>  'AB233C',
            'off_name'     =>   'asda2323sd',
            'off_head'   =>  'ma4442rk',
            'hidden_id'  => $sg->id  ,  
            'action' => 'Edit',
            ];
        $officeData = [
            'off_acr'    =>  'AB233C',
            'off_name'     =>   'asda2323sd',
            'off_head'   =>  'ma4442rk',

        ];


        $user = User::factory()->create();
        $this->assertCount(0, $user->tokens);
        $this->actingAs($user);
        $this->post('/update-office', $formData);

        // this will check if it is inserted in the database
        $response = $this->assertDatabaseHas('offices', $officeData);
    }


}

