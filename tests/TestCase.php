<?php

namespace Tests;

use App\Models\Portfolio;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * @property User $user
 * @property Profile $profile
 * @property Portfolio $portfolio
 * @property Project $project
 */
abstract class TestCase extends BaseTestCase
{
    public $user;

    public $profile;

    public $portfolio;

    public $project;
}
