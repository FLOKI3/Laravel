<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .heading-section {
            text-align: center;
        }

        .heading-section img {
            text-align: center;
            height: 50px;
            width: auto;
            display: inline-block;
        }

        h4 {
            font-size: 20px;
            margin-bottom: 0px;
            text-align: center;
            color: gray;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            vertical-align: middle;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
            font-size: 16px;
        }

        /* Class Title Styling */
        .class-title {
            font-weight: bold;
            color: #333;
        }

        .sub-title {
            font-size: 12px;
            color: gray;
            margin-top: 0px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        /* Time Styling */
        .time-text {
            color: #ff6b6b;
            font-size: 12px;
        }

        footer .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: gray;
        }

    </style>
</head>
<body>
    <section class="ftco-section">
        <div class="container">
            <!-- Logo Section -->
            <div class="heading-section">
                @php
                    $path = public_path('assets/images/logo.png');
                    $logoData = '';
                    if (file_exists($path)) {
                        $logoData = base64_encode(file_get_contents($path));
                    }
                @endphp

                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" alt="Logo">
                @else
                    <p>Logo not available</p>
                @endif
            </div>

            

            <!-- Table Title -->
            <h4>Class Schedule Table</h4>

            <!-- Table Section -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                            <th>Sunday</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedCourses = $courses->groupBy(function ($course) {
                                return \Carbon\Carbon::parse($course->startTime)->format('l');
                            });

                            $maxRows = max($groupedCourses->max(fn($dayCourses) => $dayCourses->count()), 1);
                        @endphp

                        @for ($row = 0; $row < $maxRows; $row++)
                        <tr>
                            @for ($day = 1; $day <= 7; $day++) 
                                <td>
                                    @php
                                        $dayName = \Carbon\Carbon::now()->startOfWeek()->addDays($day - 1)->format('l'); 
                                        $dailyCourses = $courses->filter(function($course) use ($dayName) {
                                            return \Carbon\Carbon::parse($course->startTime)->format('l') === $dayName;
                                        })->skip($row)->first();
                                    @endphp

                                    @if ($dailyCourses)
                                        <div class="class-title">{{ optional($dailyCourses->club)->name ?? 'No Club' }}</div>
                                        <div class="sub-title">{{ optional($dailyCourses->coach)->name ?? 'No Coach' }}</div>
                                        <div class="class-title">
                                            {{ optional($dailyCourses->lesson)->name ?? 'No Lesson' }}
                                        </div>
                                        <p class="sub-title">{{ optional($dailyCourses->room)->name ?? 'No Room' }}</p>
                                        <div class="time-text">
                                            {{ \Carbon\Carbon::parse($dailyCourses->startTime)->format('g:i a') }} - 
                                            {{ \Carbon\Carbon::parse($dailyCourses->endTime)->format('g:i a') }}
                                        </div>
                                    @else
                                        <div>-</div> 
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!--
    <footer>
        <div class='footer'>Clubs.ma</div>
    </footer>
    -->
</body>
</html>
