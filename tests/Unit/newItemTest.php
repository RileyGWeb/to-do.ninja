<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class newItemTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_adds_an_item_to_a_project()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Item::create('Item 1', 1, 1);
    }
}
