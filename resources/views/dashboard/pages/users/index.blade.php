@extends('dashboard.layout')
@section('title','User List')
@section('content')
<div class="card card-form">
  <div class="row no-gutters">
    <div class="col-lg-4 card-body">
      <p><strong class="headings-color">User List</strong></p>
      <p class="text-muted">Berikut adalah daftar pengguna yang terdaftar dalam sistem, termasuk informasi seperti nama,
        email, status verifikasi email, serta tanggal pembuatan dan pembaruan data.</p>

    </div>
    <div class="col-lg-8 card-form__body">
      <div class="table-responsive border-bottom">
        <table class="table mb-0 thead-border-top-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Email Verified At</th>
              <th>Created At</th>
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody class="list" id="userList">
            <tr>
              <td>1</td>
              <td>Michael Smith</td>
              <td>michael@example.com</td>
              <td>2025-01-10 12:34:56</td>
              <td>2025-01-01 10:00:00</td>
              <td>2025-01-12 14:20:00</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection