<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VehicleVideoTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_admin_can_upload_a_presentation_video(): void
    {
        $admin = User::firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.vehicles.store'), $this->payload([
                'video' => UploadedFile::fake()->create('presentation.mp4', 4096, 'video/mp4'),
            ]))
            ->assertRedirect(route('admin'));

        $vehicle = Vehicle::query()->where('nom', 'Vidéo Test')->firstOrFail();

        $this->assertNotNull($vehicle->video);
        $this->assertStringStartsWith('uploads/vehicles/video-', $vehicle->video);
        $this->assertStringEndsWith('.mp4', $vehicle->video);
        $this->assertFileExists(public_path($vehicle->video));
        $this->assertSame('video/mp4', $vehicle->video_mime);

        $this->get(route('vehicule.show', $vehicle))
            ->assertOk()
            ->assertSee('Vidéo de présentation')
            ->assertSee($vehicle->video, false)
            ->assertSee('type="video/mp4"', false);

        $this->get(route('catalogue'))
            ->assertOk()
            ->assertSee('▶ Vidéo', false);

        @unlink(public_path($vehicle->video));
    }

    public function test_a_non_video_file_is_rejected(): void
    {
        $admin = User::firstOrFail();

        $response = $this->actingAs($admin)
            ->post(route('admin.vehicles.store'), $this->payload([
                'video' => UploadedFile::fake()->create('document.mp4', 100, 'text/plain'),
            ]));

        $response->assertSessionHasErrors('video');
        $this->assertStringContainsString(
            'vidéo',
            (string) session('errors')->first('video')
        );

        $this->assertDatabaseMissing('vehicles', ['nom' => 'Vidéo Test']);
    }

    public function test_admin_can_remove_the_video(): void
    {
        $admin = User::firstOrFail();
        $vehicle = $this->vehicleWithVideo();

        $this->actingAs($admin)
            ->put(route('admin.vehicles.update', $vehicle), $this->payload([
                'remove_video' => 1,
            ]))
            ->assertRedirect(route('admin'));

        $vehicle->refresh();

        $this->assertNull($vehicle->video);
        $this->assertFileDoesNotExist(public_path('uploads/vehicles/'.$this->videoName));
    }

    public function test_a_new_video_replaces_the_previous_one(): void
    {
        $admin = User::firstOrFail();
        $vehicle = $this->vehicleWithVideo();
        $oldPath = public_path($vehicle->video);

        $this->actingAs($admin)
            ->put(route('admin.vehicles.update', $vehicle), $this->payload([
                'video' => UploadedFile::fake()->create('nouvelle.mp4', 2048, 'video/mp4'),
            ]))
            ->assertRedirect(route('admin'));

        $vehicle->refresh();

        $this->assertStringStartsWith('uploads/vehicles/video-', (string) $vehicle->video);
        $this->assertFileExists(public_path($vehicle->video));
        $this->assertFileDoesNotExist($oldPath);

        @unlink(public_path($vehicle->video));
    }

    public function test_deleting_a_vehicle_deletes_its_video(): void
    {
        $admin = User::firstOrFail();
        $vehicle = $this->vehicleWithVideo();
        $path = public_path($vehicle->video);

        $this->actingAs($admin)
            ->delete(route('admin.vehicles.destroy', $vehicle))
            ->assertRedirect(route('admin'));

        $this->assertFileDoesNotExist($path);
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    /**
     * Crée un véhicule avec une vidéo factice sur le disque.
     */
    private function vehicleWithVideo(): Vehicle
    {
        $this->videoName = 'video-test-'.uniqid().'.mp4';
        $directory = public_path('uploads/vehicles');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($directory.'/'.$this->videoName, 'fake-video');

        return Vehicle::create([
            'slug' => 'video-'.uniqid(),
            'nom' => 'Vidéo Test',
            'marque' => 'Test',
            'categorie' => 'SUV',
            'etat' => 'Occasion',
            'annee' => 2022,
            'carburant' => 'Essence',
            'boite' => 'Automatique',
            'puissance' => '—',
            'km' => '1000',
            'places' => 5,
            'image' => 'assets/car-storm.jpg',
            'galerie' => ['assets/car-storm.jpg'],
            'video' => 'uploads/vehicles/'.$this->videoName,
            'prix_vente' => 1000,
            'disponible' => true,
            'description' => 'test',
        ]);
    }

    private string $videoName = '';

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nom' => 'Vidéo Test',
            'marque' => 'Test',
            'categorie' => 'SUV',
            'etat' => 'Occasion',
            'annee' => 2022,
            'carburant' => 'Essence',
            'boite' => 'Automatique',
            'km' => '1000',
            'prix_vente' => 1000,
            'description' => 'test',
            'disponible' => 1,
        ], $overrides);
    }
}
