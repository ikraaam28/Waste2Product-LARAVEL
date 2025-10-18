<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\EventFeedback;
use App\Models\Badge;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventParticipationEmail;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use DatabaseTransactions;

    private User $adminUser;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'terms_accepted' => true
        ]);

        // Create regular user
        $this->regularUser = User::create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
            'terms_accepted' => true
        ]);
    }

    /** @test */
    public function admin_can_view_event_dashboard()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'date' => now()->addDays(7),
            'location' => 'Test Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/events/dashboard');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.events.dashboard');
        $response->assertViewHas(['totalEvents', 'totalParticipants', 'totalScanned', 'totalBadges']);
    }

    /** @test */
    public function admin_can_view_events_index()
    {
        // Arrange
        Event::create([
            'title' => 'Event 1',
            'description' => 'Description 1',
            'date' => now()->addDays(7),
            'location' => 'Location 1',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        Event::create([
            'title' => 'Event 2',
            'description' => 'Description 2',
            'date' => now()->addDays(14),
            'location' => 'Location 2',
            'max_participants' => 30,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/events');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.events.index');
        $response->assertViewHas('events');
    }

    /** @test */
    public function admin_can_create_event()
    {
        // Arrange
        $eventData = [
            'title' => 'New Event',
            'description' => 'New Event Description',
            'date' => now()->addDays(7)->format('Y-m-d'),
            'time' => '14:00',
            'location' => 'New Location',
            'max_participants' => 100,
            'is_active' => true
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post('/admin/events', $eventData);

        // Assert
        $response->assertRedirect('/admin/events');
        $this->assertDatabaseHas('events', [
            'title' => 'New Event',
            'description' => 'New Event Description',
            'location' => 'New Location',
            'max_participants' => 100,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);
    }

    /** @test */
    public function admin_can_update_event()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Original Event',
            'description' => 'Original Description',
            'date' => now()->addDays(7),
            'location' => 'Original Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        $updateData = [
            'title' => 'Updated Event',
            'description' => 'Updated Description',
            'date' => now()->addDays(14)->format('Y-m-d'),
            'time' => '15:00',
            'location' => 'Updated Location',
            'max_participants' => 75,
            'is_active' => true
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->put("/admin/events/{$event->id}", $updateData);

        // Assert
        $response->assertRedirect('/admin/events');
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event',
            'description' => 'Updated Description',
            'location' => 'Updated Location',
            'max_participants' => 75
        ]);
    }

    /** @test */
    public function admin_can_delete_event()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Event to Delete',
            'description' => 'Description',
            'date' => now()->addDays(7),
            'location' => 'Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->delete("/admin/events/{$event->id}");

        // Assert
        $response->assertRedirect('/admin/events');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /** @test */
    public function admin_can_view_event_details()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Event Details',
            'description' => 'Event Description',
            'date' => now()->addDays(7),
            'location' => 'Event Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get("/admin/events/{$event->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.events.show');
        $response->assertViewHas('event', $event);
    }

    /** @test */
    public function admin_can_export_event_participants()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Export Event',
            'description' => 'Event for Export',
            'date' => now()->addDays(7),
            'location' => 'Export Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        // Add participants
        $event->participants()->attach($this->regularUser->id, [
            'scanned_at' => now(),
            'badge_earned' => true
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get("/admin/events/{$event->id}/export");

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_scan_participant()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Scan Event',
            'description' => 'Event for Scanning',
            'date' => now()->addDays(7),
            'location' => 'Scan Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        $event->participants()->attach($this->regularUser->id);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post("/admin/events/{$event->id}/scan", [
                'user_id' => $this->regularUser->id
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('event_participants', [
            'event_id' => $event->id,
            'user_id' => $this->regularUser->id,
            'scanned_at' => now()
        ]);
    }

    /** @test */
    public function admin_can_award_badge_to_participant()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Badge Event',
            'description' => 'Event for Badges',
            'date' => now()->addDays(7),
            'location' => 'Badge Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        $badge = Badge::create([
            'name' => 'Test Badge',
            'description' => 'Test Badge Description',
            'icon' => 'fas fa-star',
            'color' => '#FFD700'
        ]);

        $event->participants()->attach($this->regularUser->id);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post("/admin/events/{$event->id}/badge", [
                'user_id' => $this->regularUser->id,
                'badge_id' => $badge->id
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $this->regularUser->id,
            'badge_id' => $badge->id,
            'event_id' => $event->id
        ]);
    }

    /** @test */
    public function admin_can_view_event_feedbacks()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Feedback Event',
            'description' => 'Event for Feedback',
            'date' => now()->addDays(7),
            'location' => 'Feedback Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        EventFeedback::create([
            'event_id' => $event->id,
            'user_id' => $this->regularUser->id,
            'rating' => 5,
            'comment' => 'Great event!',
            'is_anonymous' => false
        ]);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get("/admin/events/{$event->id}/feedbacks");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.events.feedbacks');
        $response->assertViewHas(['event', 'feedbacks']);
    }

    /** @test */
    public function admin_can_upload_event_image()
    {
        // Arrange
        Storage::fake('public');
        $event = Event::create([
            'title' => 'Image Event',
            'description' => 'Event with Image',
            'date' => now()->addDays(7),
            'location' => 'Image Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        $file = UploadedFile::fake()->image('event.jpg');

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post("/admin/events/{$event->id}/image", [
                'image' => $file
            ]);

        // Assert
        $response->assertRedirect("/admin/events/{$event->id}");
        Storage::disk('public')->assertExists('events/' . $file->hashName());
    }

    /** @test */
    public function admin_can_send_participation_email()
    {
        // Arrange
        Mail::fake();
        
        $event = Event::create([
            'title' => 'Email Event',
            'description' => 'Event for Email',
            'date' => now()->addDays(7),
            'location' => 'Email Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        $event->participants()->attach($this->regularUser->id);

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post("/admin/events/{$event->id}/send-email", [
                'user_id' => $this->regularUser->id
            ]);

        // Assert
        $response->assertStatus(200);
        Mail::assertSent(EventParticipationEmail::class, function ($mail) use ($event, $regularUser) {
            return $mail->hasTo($this->regularUser->email) && 
                   $mail->event->id === $event->id;
        });
    }

    /** @test */
    public function regular_user_cannot_access_admin_event_routes()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'date' => now()->addDays(7),
            'location' => 'Test Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        // Act & Assert
        $this->actingAs($this->regularUser)
            ->get('/admin/events/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->regularUser)
            ->get('/admin/events')
            ->assertStatus(403);

        $this->actingAs($this->regularUser)
            ->get("/admin/events/{$event->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_admin_event_routes()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'date' => now()->addDays(7),
            'location' => 'Test Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        // Act & Assert
        $this->get('/admin/events/dashboard')
            ->assertRedirect('/login');

        $this->get('/admin/events')
            ->assertRedirect('/login');

        $this->get("/admin/events/{$event->id}")
            ->assertRedirect('/login');
    }

    /** @test */
    public function event_creation_requires_valid_data()
    {
        // Arrange
        $invalidData = [
            'title' => '', // Empty title
            'description' => 'Test Description',
            'date' => 'invalid-date',
            'location' => 'Test Location',
            'max_participants' => -1 // Invalid participants
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->post('/admin/events', $invalidData);

        // Assert
        $response->assertSessionHasErrors(['title', 'date', 'max_participants']);
        $this->assertDatabaseMissing('events', ['description' => 'Test Description']);
    }

    /** @test */
    public function event_update_requires_valid_data()
    {
        // Arrange
        $event = Event::create([
            'title' => 'Original Event',
            'description' => 'Original Description',
            'date' => now()->addDays(7),
            'location' => 'Original Location',
            'max_participants' => 50,
            'is_active' => true,
            'created_by' => $this->adminUser->id
        ]);

        $invalidData = [
            'title' => '', // Empty title
            'description' => 'Updated Description',
            'date' => 'invalid-date',
            'location' => 'Updated Location',
            'max_participants' => -1
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
            ->put("/admin/events/{$event->id}", $invalidData);

        // Assert
        $response->assertSessionHasErrors(['title', 'date', 'max_participants']);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Original Event' // Should remain unchanged
        ]);
    }
}
