<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $otherUser;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed role (jika controller pakai role)
        $this->seed(['RoleSeeder']);

        // =========================
        // USER
        // =========================
        $this->owner = User::forceCreate([
            'name' => 'Event Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->otherUser = User::forceCreate([
            'name' => 'Other User',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
        ]);

        // =========================
        // EVENT
        // =========================
        $this->event = Event::forceCreate([
            'user_id' => $this->owner->id,
            'event_title' => 'Laravel Test Meetup',
            'organizer_name' => 'Test Organizer',
            'organizer_type' => 'Komunitas',
            'event_category' => 'Technology',
            'event_description' => 'A test event description.',
            'start_date' => Carbon::now()->addDays(7)->toDateString(),
            'start_time' => '10:00:00',
            'end_date' => Carbon::now()->addDays(8)->toDateString(),
            'end_time' => '12:00:00',
            'event_location' => 'Online',
            'contact_person' => '0812345678',
            'status' => 'approved',
        ]);

        // =========================
        // REGISTRATION
        // =========================
        EventRegistration::forceCreate([
            'user_id' => $this->owner->id,
            'event_id' => $this->event->id,
            'status' => 'approved',
        ]);
    }

    // ==================================================
    // 1. USER CAN ACCESS CREATE EVENT FORM
    // ==================================================
    public function test_user_can_access_create_event_form()
    {
        $response = $this->actingAs($this->owner)
            ->get(route('submit-event.form'));

        $response->assertStatus(200);
    }

    // ==================================================
    // 2. USER CAN SUBMIT EVENT
    // ==================================================
    public function test_user_can_submit_event()
    {
        $payload = [
            'event_title' => 'Event Submit User',
            'organizer_name' => 'Organizer User',
            'organizer_type' => 'Komunitas',
            'event_category' => 'Technology',
            'event_description' => 'Deskripsi submit oleh user login',
            'start_date' => Carbon::now()->addDays(5)->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::now()->addDays(6)->toDateString(),
            'end_time' => '12:00',
            'event_location' => 'Online',
            'registration_link' => 'https://example.com/register',
            'contact_person' => '08123456789',
            'event_poster' => null,
        ];

        $response = $this->actingAs($this->owner)
            ->post(route('submit-event.form'), $payload);

        $response->assertStatus(302);

        $this->assertDatabaseHas('events', [
            'event_title' => 'Event Submit User',
            'user_id' => $this->owner->id,
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_access_submit_event()
{
    $payload = [
        'event_title' => 'Guest Event',
        'organizer_name' => 'Guest Organizer',
        'organizer_type' => 'Komunitas',
        'event_category' => 'Technology',
        'event_description' => 'Guest mencoba submit event',
        'start_date' => Carbon::now()->addDays(5)->toDateString(),
        'start_time' => '09:00',
        'end_date' => Carbon::now()->addDays(6)->toDateString(),
        'end_time' => '12:00',
        'event_location' => 'Online',
        'registration_link' => 'https://example.com/register',
        'contact_person' => '08123456789',
        'event_poster' => null,
    ];

    // Guest (tidak login) mencoba submit event
    $response = $this->post(route('submit-event.form'), $payload);

    // Harus redirect ke login (karena middleware auth)
    $response->assertRedirect(route('login'));
}

    // ==================================================
    // 3. OWNER CAN ACCESS EDIT EVENT FORM
    // ==================================================
    public function test_owner_can_access_edit_event_form()
    {
        $this->event->update(['status' => 'pending']);

        $response = $this->actingAs($this->owner)
            ->get(route('submit-event.form', ['edit' => $this->event->id]));

        $response->assertStatus(200);
    }

    // ==================================================
    // 4. OTHER USER CANNOT ACCESS EDIT (IDOR PREVENTION)
    // ==================================================
    public function test_other_user_cannot_access_edit_event_form_idor_prevention()
    {
        $this->event->update(['status' => 'pending']);

        $response = $this->actingAs($this->otherUser)
            ->get(route('submit-event.form', ['edit' => $this->event->id]));

        $response->assertRedirect(route('user.event.history'));
        $response->assertSessionHas('error', 'Anda tidak berhak mengubah event ini.');
    }
}