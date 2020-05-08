@extends('layouts.popup')

<h3>여기는 업데이트 로그 전체보기 영역</h3>

@section('content')

<table cellpadding="10" class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
            <th>수정일자</th>
            <th>계정이름</th>
            <th>아이피 주소</th>
            <th>변경 내용</th>
        </tr>
    </thead>
    @foreach($product_update_logs_all as $log)
        <tr>
            <td>{{ $log->updated_at }}</td>
            <td>{{ $log->seller->name }}</td>
            <td>{{ $log->ip_address }}</td>
            <td><pre>{{ $log->log_description }}</pre></td>
        </tr>
    @endforeach
</table>

@endsection

