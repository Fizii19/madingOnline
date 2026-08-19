<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Seed the posts table with realistic sample data.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@mading.test'],
            ['name' => 'Admin Mading', 'password' => 'password', 'is_admin' => true]
        );

        $posts = [
            [
                'title' => 'Annual Spring Festival Announced',
                'category' => 'event',
                'status' => 'published',
                'is_pinned' => true,
                'views' => 1248,
                'created_at' => now()->subHours(5),
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB0a-A4E0XfIO4TZ03KMLZey_uZXP5gQNFibOqFBVmWw2iWahJbbEPOpvU-FQWSgw5N_TEep1Kw0Lnjl284Ecv8YE1wvZaxvfORfocE6EbRoxhDKLcWgGcvtTbyIBAO0D9qfaMoJdod489BDtKvsumwJwEPdvTDsiQVNVcaPwjYlPerNVDigWkdGwyoc6CkeZBCD7c34LDQ27RKkSV0uYtuAgFH_q9U-vYJAKALEx2okYdrQq0g1OJiZQ',
                'content' => "Get ready for the biggest event of the year! The Annual Spring Festival brings together all student organizations for three days of activities, performances, and food stalls.\n\nEarly bird registration for booths opens next week. Each organization is encouraged to prepare a booth, a performance slot, or a food stall to showcase its members' talents.\n\nDon't miss the opening ceremony on the first day at the main stage — we'll kick things off with a parade around the campus courtyard followed by live music and a community barbecue.",
            ],
            [
                'title' => 'Midterm Schedule Released',
                'category' => 'academic',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 864,
                'created_at' => now()->subDays(1)->subHours(2),
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDmw5ZYaXDre7RX2i1alqd_M2xlCYUZUFUDi_dMrC8mcT9FHzz6PQu7-0sqO0Rmqd_W5XoOD17jPrjTJxFC7yCklY5iIV_rbyhEKLXI7eN0pwmsx6eZli55sTVsRvGGaHZ_xjbwFO5N6IfqEPUWtA2m1U1RlGjLHJvqdLjJB9uNX83OQjuhFzPoDLRrh4tOAd2xnAE1fMQn2Ow318d1LyqlD9vwV75j5_qz92H4ebOTlJq8LrFCqUsrsA',
                'content' => "The finalized schedule for the Fall semester midterms is now available. Please check your department's specific guidelines for room assignments and allowed materials.\n\nAll exams will be held in their assigned rooms between 08:00 and 17:00. Students with schedule conflicts should contact the academic office at least one week before the exam window closes.",
            ],
            [
                'title' => 'Library Renovation Notice',
                'category' => 'alert',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 532,
                'created_at' => now()->subDays(2)->subHours(6),
                'image_url' => null,
                'content' => "The East Wing of the central library will be closed for structural renovations starting next Monday. Study spaces have been temporarily relocated to the student center.\n\nExpected completion is end of the month. We apologize for the inconvenience and appreciate your patience while we upgrade the reading rooms.",
            ],
            [
                'title' => 'Robotics Team Wins Regionals',
                'category' => 'club',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 421,
                'created_at' => now()->subDays(3)->subHours(1),
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC9yqQhaPs0sSbLItcQVTFotDDFBGsEJLuIwt3OBfHEjUdFSC5i2hPr2VsIMeoN5lQJusSMtlNHvpRQR529ifbuloreovHVCf4kEczPfzorRwb_FvrAsQElX3iya1QFToh-g08cEg4voBkzncRDpTHktQrq9Edn0lYhQIppPsZWReLVH0ahb2AjhVCDiHEJ_meU5tkSukW5zZywozp65-llYxpljcAx2eRnQNMjyAhX1B8xWcMTzWBaTA',
                'content' => "Congratulations to our university robotics team for securing first place at the state regional competition this weekend.\n\nThe team's autonomous rover beat 24 other universities in the obstacle course and payload delivery rounds. They now advance to the national finals next semester — come support them!",
            ],
            [
                'title' => 'Q3 Financial Results Breakdown',
                'category' => 'finance',
                'status' => 'pending',
                'is_pinned' => false,
                'views' => 0,
                'created_at' => now()->subDays(4)->subHours(4),
                'image_url' => null,
                'content' => "The finance committee has compiled the Q3 budget report for the student organizations. The document outlines spending across events, equipment, and operational costs.\n\nA summary session will be held this Friday at the student center for all organization treasurers.",
            ],
            [
                'title' => 'New Office Policy Guidelines',
                'category' => 'hr',
                'status' => 'pending',
                'is_pinned' => false,
                'views' => 0,
                'created_at' => now()->subDays(5)->subHours(3),
                'image_url' => null,
                'content' => "Updated office policy guidelines are now in effect for all student organization rooms. Key changes include new booking hours and a revised equipment loan procedure.\n\nPlease review the full guidelines before the next organization meeting.",
            ],
            [
                'title' => 'Cafeteria Menu Update',
                'category' => 'announcement',
                'status' => 'draft',
                'is_pinned' => false,
                'views' => 0,
                'created_at' => now()->subDays(6)->subHours(7),
                'image_url' => null,
                'content' => "Draft announcement about the upcoming cafeteria menu rotation and new vendor partnerships for the next semester.",
            ],
            [
                'title' => 'Science Fair Booth Registration Open',
                'category' => 'event',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 318,
                'created_at' => now()->subDays(7)->subHours(2),
                'image_url' => null,
                'content' => "Booth registration for the annual science fair is now open. Clubs, classes, and student teams can reserve a booth to show experiments, prototypes, and demos.\n\nSlots will be assigned on a first-come, first-served basis. Bring your own display boards and power extension if needed.",
            ],
            [
                'title' => 'Campus Wi-Fi Maintenance Window',
                'category' => 'alert',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 246,
                'created_at' => now()->subDays(8)->subHours(5),
                'image_url' => null,
                'content' => "The campus Wi-Fi network will undergo maintenance this weekend between 22:00 and 02:00. Temporary interruptions may occur in classrooms and the library.\n\nIf you need stable access, please download required materials in advance.",
            ],
            [
                'title' => 'Debate Club Practice Match Results',
                'category' => 'club',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 198,
                'created_at' => now()->subDays(9)->subHours(1),
                'image_url' => null,
                'content' => "The debate club held a practice match against the regional team yesterday. The event helped new members improve argument structure and rebuttal timing.\n\nTraining will continue every Wednesday after school for anyone interested in joining.",
            ],
            [
                'title' => 'Exam Hall Seating Chart Published',
                'category' => 'academic',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 402,
                'created_at' => now()->subDays(10)->subHours(3),
                'image_url' => null,
                'content' => "The seating chart for the final exam hall has been published in the academic office and on the student portal. Please check your seat number before exam day.\n\nStudents who find duplicate or missing names should report them before Friday afternoon.",
            ],
            [
                'title' => 'Student Council Volunteer Drive',
                'category' => 'announcement',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 271,
                'created_at' => now()->subDays(11)->subHours(4),
                'image_url' => null,
                'content' => "The student council is recruiting volunteers for the next community outreach program. Volunteers will help with registration, logistics, and event coordination.\n\nSign-up sheets are available at the student council office until the end of the week.",
            ],
            [
                'title' => 'Scholarship Info Session Reminder',
                'category' => 'finance',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 167,
                'created_at' => now()->subDays(12)->subHours(6),
                'image_url' => null,
                'content' => "Reminder for all scholarship applicants: the information session starts tomorrow at 15:00 in the multipurpose room. Attendance is recommended for students who still need help with document submission.\n\nBring a notebook if you want to record the checklist and deadlines.",
            ],
            [
                'title' => 'New Club Room Allocation',
                'category' => 'hr',
                'status' => 'published',
                'is_pinned' => false,
                'views' => 221,
                'created_at' => now()->subDays(13)->subHours(2),
                'image_url' => null,
                'content' => "Updated room allocation for student clubs has been posted at the administration board. Each club should verify its assigned room number before the new semester begins.\n\nAny change requests must be submitted through the school office by Tuesday.",
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['title' => $post['title']],
                [
                    'user_id' => $admin->id,
                    'category' => $post['category'],
                    'status' => $post['status'],
                    'is_pinned' => $post['is_pinned'],
                    'views' => $post['views'],
                    'created_at' => $post['created_at'],
                    'updated_at' => $post['created_at'],
                    'image_url' => $post['image_url'],
                    'content' => $post['content'],
                ]
            );
        }

        // Sample comments
        $springFestival = Post::where('title', 'Annual Spring Festival Announced')->first();
        $robotics = Post::where('title', 'Robotics Team Wins Regionals')->first();

        $springFestival?->comments()->firstOrCreate(
            ['user_id' => $admin->id, 'body' => 'Seru banget! Sudah tidak sabar mau ikut booth tahun ini. 🎉'],
            ['created_at' => now()->subHours(3)]
        );
        $springFestival?->comments()->firstOrCreate(
            ['user_id' => $admin->id, 'body' => 'Apakah pendaftaran booth ditutup minggu depan?'],
            ['created_at' => now()->subHours(1)]
        );
        $robotics?->comments()->firstOrCreate(
            ['user_id' => $admin->id, 'body' => 'Selamat tim! 🏆'],
            ['created_at' => now()->subDay()]
        );
    }
}
