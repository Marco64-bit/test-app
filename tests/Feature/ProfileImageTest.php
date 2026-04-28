<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileImageTest extends TestCase
{
    use RefreshDatabase; // Resets the database after the test

    public function test_user_cannot_upload_png_image()
    {
        // 1. Create a fake user and log them in
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Create a fake PNG file
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.png');

        // 3. Send the POST request
        $response = $this->postJson('/api/profile/image', [
            'profile_image' => $file,
        ]);

        // 4. Assert the system rejected it for having the wrong extension
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['profile_image']);
    }
}
