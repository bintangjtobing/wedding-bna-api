@extends('dashboard.layout')
@section('title','Guests List')
@section('content')
<div class="card card-form">
  <div class="row no-gutters">
    <div class="col-lg-3 card-body">
      <p><strong class="headings-color">Guest List</strong></p>
      <p class="text-muted">Berikut adalah daftar tamu yang telah diundang dalam sistem, mencakup informasi seperti
        nama,
        nomor telepon, email, dan status kehadiran.</p>
      <form method="POST" action="/send-invitations" id="sendInvitationForm">
        @csrf
        <button type="submit" class="btn btn-primary">Send Invitations</button>
      </form>
    </div>
    <div class="col-lg-9 card-form__body">
      <div class="table-responsive border-bottom">
        <table class="table mb-0 thead-border-top-0">
          <thead>
            <tr>
              <th>Info</th>
              <th>Friend Of</th>
              <th>Attendance Name</th>
              <th>Attendance Message</th>
              <th>Attend</th>
            </tr>
          </thead>
          <tbody class="list" id="guestList">
            @foreach ($guests as $guest)
            <tr>
              <td>
                <strong>{{ $guest->specific_call ?? 'Kak' }}&nbsp;{{ $guest->name }}</strong><br>
                <span class="text-muted">{{ $guest->email }}</span><br>
                <span class="text-muted">{{ $guest->phone_number }}</span>
              </td>
              <td>{{ $guest->friend_of ?? 'N/A' }}</td>
              <td>{{ $guest->attendance_name ?? 'N/A' }}</td>
              <td>{{ $guest->attendance_message ?? 'N/A' }}</td>
              <td>
                @if ($guest->attend == 1)
                <span class="text-success"><i class="material-icons">check_circle</i> Yes</span>
                @elseif ($guest->attend == 0)
                <span class="text-danger"><i class="material-icons">cancel</i> No</span>
                @else
                <span class="text-warning"><i class="material-icons">help</i> Maybe</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection