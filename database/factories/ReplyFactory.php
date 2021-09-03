<?php

namespace Database\Factories;

use App\Models\Reply;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReplyFactory extends Factory
{
    protected $model = Reply::class;

    public function definition(): array
    {
        return [
            'content'=>$this->faker->sentence(),
            'topic_id'=>random_int(1,100),
            'user_id'=>random_int(1,10),
        ];
    }
}
