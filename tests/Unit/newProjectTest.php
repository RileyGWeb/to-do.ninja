<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;

class newProjectTest extends TestCase
{
    /**
     * @return void
     */
    public function test_it_creates_a_new_project ()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Project::create('Project 1');
    }
}
