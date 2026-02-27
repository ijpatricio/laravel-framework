<?php

namespace Illuminate\Tests\Foundation\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class VendorPublishCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        ServiceProvider::$publishes = [];
        ServiceProvider::$publishGroups = [];
    }

    public function testReturnsSuccessExitCodeWhenNothingToPublish()
    {
        $this->artisan('vendor:publish', ['--all' => true])
            ->assertSuccessful();
    }

    public function testReturnsFailureExitCodeWhenPathCannotBeLocated()
    {
        ServiceProvider::$publishes[VendorPublishTestProvider::class] = [
            '/non/existent/path' => '/some/destination',
        ];

        $this->artisan('vendor:publish', ['--all' => true])
            ->assertFailed();
    }

    public function testReturnsSuccessExitCodeWhenFilePublishedSuccessfully()
    {
        $from = __FILE__;
        $to = sys_get_temp_dir().'/vendor-publish-test/'.basename(__FILE__);

        ServiceProvider::$publishes[VendorPublishTestProvider::class] = [
            $from => $to,
        ];

        $this->artisan('vendor:publish', ['--all' => true, '--force' => true])
            ->assertSuccessful();

        @unlink($to);
        @rmdir(dirname($to));
    }
}

class VendorPublishTestProvider extends ServiceProvider
{
    public function register() {}
}
