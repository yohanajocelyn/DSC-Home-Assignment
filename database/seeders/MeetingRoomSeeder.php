<?php

namespace Database\Seeders;

use App\Models\MeetingRoom;
use Illuminate\Database\Seeder;

class MeetingRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Executive Room A',
                'location' => 'Floor 3, Wing A',
                'capacity' => 16,
                'description' => '<p>High-end executive meeting room equipped with a 4K display, video conferencing system, whiteboard, and ergonomic seating.</p>',
                'is_active' => true,
            ],
            [
                'name' => 'Sapphire Executive Hall',
                'location' => 'Building 2, Level 4',
                'capacity' => 20,
                'description' => '<p>Spacious conference hall suitable for department meetings, client presentations, and hybrid workshops. Features dual presentation displays and surround sound.</p>',
                'is_active' => true,
            ],
            [
                'name' => 'Innovation Hub B',
                'location' => 'Floor 2, Wing B',
                'capacity' => 8,
                'description' => '<p>Modern collaborative space designed for team brainstorming, sprint planning, and small group discussions. Includes interactive whiteboard and display screen.</p>',
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $room) {
            MeetingRoom::updateOrCreate(
                ['name' => $room['name']],
                $room
            );
        }
    }
}
