<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $testimonials = [];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(['id' => $testimonial['id']], $testimonial);
        }
    }
}
