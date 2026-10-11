<?php

namespace Tests\Unit;

use App\Http\Requests\Admin\StoreActivityRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreActivityRequestTest extends TestCase
{
    public function test_category_is_prohibited(): void
    {
        $request = StoreActivityRequest::create('/admin/activities', 'POST', [
            'title' => 'Activity title',
            'body' => 'Activity story.',
            'status' => 'draft',
            'category' => 'Not allowed',
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('category', $validator->errors()->toArray());
    }

    public function test_published_at_is_prohibited(): void
    {
        $request = StoreActivityRequest::create('/admin/activities', 'POST', [
            'title' => 'Activity title',
            'body' => 'Activity story.',
            'status' => 'published',
            'published_at' => now()->addDay()->toIso8601String(),
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('published_at', $validator->errors()->toArray());
    }

    public function test_no_more_than_ten_images_are_allowed(): void
    {
        $images = array_map(
            fn (int $index): array => [
                'file' => UploadedFile::fake()->create("activity-{$index}.jpg", 100, 'image/jpeg'),
            ],
            range(1, 11),
        );

        $request = StoreActivityRequest::create('/admin/activities', 'POST', [
            'title' => 'Activity title',
            'body' => 'Activity story.',
            'status' => 'draft',
            'images' => $images,
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('images', $validator->errors()->toArray());
    }

    public function test_image_description_is_not_required(): void
    {
        $request = StoreActivityRequest::create('/admin/activities', 'POST', [
            'title' => 'Activity title',
            'body' => 'Activity story.',
            'status' => 'draft',
            'images' => [[
                'file' => UploadedFile::fake()->image('activity.jpg'),
            ]],
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }
}
