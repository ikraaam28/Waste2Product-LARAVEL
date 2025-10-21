<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function test_full_name_and_role_helpers(): void
    {
        $user = new User([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->assertSame('John Doe', $user->full_name);
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isUser());
        $this->assertFalse($user->isSupplier());
        $this->assertTrue($user->isActive());
        $this->assertSame('Administrateur', $user->role_label);
        $this->assertSame('danger', $user->role_badge_color);
        $this->assertSame('fas fa-crown', $user->role_icon);
    }
}



