<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\ItemList;

class newListTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_adds_a_list_to_a_project()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        ItemList::create('List 1', 1);
    }
}
