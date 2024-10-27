<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
<h1>Students List</h1>
<h4>Exported on {{ now()->toFormattedDateString() }} at {{ now()->toTimeString() }}</h4>
<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Class</th>
        <th>Section</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($students as $student)
        <tr>
            <td>{{ $student->name }}</td>
            <td>{{ $student->email }}</td>
            <td>{{ $student->class->name }}</td>
            <td>{{ $student->section->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
