<?php

namespace Tests\Feature;

use App\Models\CoffeeDuty;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_public_home_is_available_without_login(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        $ana = Employee::create([
            'name' => 'Ana',
            'queue_position' => 1,
            'active' => true,
        ]);
        $bruno = Employee::create([
            'name' => 'Bruno',
            'queue_position' => 2,
            'active' => true,
        ]);
        CoffeeDuty::create([
            'employee_id' => $ana->id,
            'duty_date' => '2026-06-08',
        ]);
        CoffeeDuty::create([
            'employee_id' => $bruno->id,
            'duty_date' => '2026-06-09',
        ]);

        $response = $this->get(route('home'))
            ->assertOk()
            ->assertSee('Histórico recente')
            ->assertDontSee('Trocas em aberto')
            ->assertDontSee('Concluir');

        $this->assertSame(2, substr_count($response->getContent(), 'data-history-swap-form'));

        Carbon::setTestNow();
    }

    public function test_admin_dashboard_requires_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_can_login(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_admin_can_swap_today_with_selected_employee(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $ana = Employee::create([
            'name' => 'Ana',
            'queue_position' => 1,
            'active' => true,
        ]);
        Employee::create([
            'name' => 'Bruno',
            'queue_position' => 2,
            'active' => true,
        ]);
        $carlos = Employee::create([
            'name' => 'Carlos',
            'queue_position' => 3,
            'active' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.escala.swap', '2026-06-10'), [
                'replacement_employee_ids' => [$carlos->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $duty = CoffeeDuty::whereDate('duty_date', '2026-06-10')->firstOrFail();
        $counterpart = CoffeeDuty::whereDate('duty_date', '2026-06-12')->firstOrFail();

        $this->assertSame($carlos->id, $duty->employee_id);
        $this->assertSame($ana->id, $duty->original_employee_id);
        $this->assertSame($ana->id, $counterpart->employee_id);
        $this->assertSame($carlos->id, $counterpart->original_employee_id);

        Carbon::setTestNow();
    }

    public function test_public_can_register_a_swap_after_the_duty_date_within_the_open_window(): void
    {
        Carbon::setTestNow('2026-06-11 09:00:00');

        $ana = Employee::create([
            'name' => 'Ana',
            'queue_position' => 1,
            'active' => true,
        ]);
        Employee::create([
            'name' => 'Bruno',
            'queue_position' => 2,
            'active' => true,
        ]);
        $carlos = Employee::create([
            'name' => 'Carlos',
            'queue_position' => 3,
            'active' => true,
        ]);
        CoffeeDuty::create([
            'employee_id' => $ana->id,
            'duty_date' => '2026-06-10',
        ]);

        $this->patch(route('schedule.swap', '2026-06-10'), [
            'replacement_employee_ids' => [$carlos->id],
            'swap_date' => '2026-06-10',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('coffee_duties', [
            'duty_date' => '2026-06-10 00:00:00',
            'employee_id' => $carlos->id,
            'original_employee_id' => $ana->id,
        ]);

        Carbon::setTestNow();
    }

    public function test_public_cannot_register_a_swap_after_the_three_business_day_window(): void
    {
        Carbon::setTestNow('2026-06-12 09:00:00');

        $ana = Employee::create([
            'name' => 'Ana',
            'queue_position' => 1,
            'active' => true,
        ]);
        $bruno = Employee::create([
            'name' => 'Bruno',
            'queue_position' => 2,
            'active' => true,
        ]);
        CoffeeDuty::create([
            'employee_id' => $ana->id,
            'duty_date' => '2026-06-08',
        ]);

        $this->patch(route('schedule.swap', '2026-06-08'), [
            'replacement_employee_ids' => [$bruno->id],
            'swap_date' => '2026-06-08',
        ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('coffee_duties', [
            'duty_date' => '2026-06-08 00:00:00',
            'employee_id' => $ana->id,
            'original_employee_id' => null,
        ]);

        Carbon::setTestNow();
    }
}
