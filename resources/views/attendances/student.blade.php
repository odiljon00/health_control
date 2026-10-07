@extends('layouts.app')
@section('title', 'Davomat — '.$student->full_name)

@section('content')
<div class="card">
    <h2>{{ $student->full_name }}</h2>
    <p style="color:#667;">{{ $student->class_name }} • {{ $student->school->name ?? '—' }}</p>
</div>

<div class="card">
    <h3>Oylik statistika</h3>
    @if($monthlyStats->count())
        <table>
            <thead>
                <tr><th>Oy</th><th>✅ Keldi</th><th>❌ Kelmadi</th><th>⏰ Kechikdi</th><th>📝 Sababli</th></tr>
            </thead>
            <tbody>
                @foreach($monthlyStats as $row)
                    <tr>
                        <td><strong>{{ $row->month }}</strong></td>
                        <td><span class="badge green">{{ $row->present }}</span></td>
                        <td><span class="badge red">{{ $row->absent }}</span></td>
                        <td><span class="badge yellow">{{ $row->late }}</span></td>
                        <td><span class="badge navy">{{ $row->excused }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color:#889;">Ma'lumot yo'q</p>
    @endif
</div>

<div class="card">
    <h3>So'nggi yozuvlar</h3>
    <table>
        <thead>
            <tr><th>Sana</th><th>Holat</th><th>Sabab</th><th>Belgilagan</th></tr>
        </thead>
        <tbody>
            @foreach($attendances as $a)
                <tr>
                    <td>{{ $a->attendance_date->format('d.m.Y') }}</td>
                    <td>
                        <span class="badge"
                              style="background:{{ $a->status_color }}22;color:{{ $a->status_color }};">
                            {{ $a->status_label }}
                        </span>
                    </td>
                    <td>{{ $a->reason ?? '—' }}</td>
                    <td>{{ $a->marker->name ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px;">{{ $attendances->links() }}</div>
</div>
@endsection