<!DOCTYPE html>
<html>
<head>
    <title>Hasil Data</title>
</head>
<body>

<h2>Hasil Process Data</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>Case ID</th>
        <th>PN</th>
        <th>Kategori</th>
        <th>Harga</th>
    </tr>

    @foreach ($data as $index => $row)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $row->case_id }}</td>
        <td>{{ $row->pn }}</td>
        <td>{{ $row->kategori }}</td>
        <td>{{ $row->harga }}</td>
    </tr>
    @endforeach

</table>

</body>
</html>