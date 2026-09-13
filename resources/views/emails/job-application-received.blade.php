<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #1f2937; line-height: 1.5;">
    <h2>New application received</h2>
    <p><strong>Job:</strong> {{ $application->jobPosting->title }} ({{ $application->jobPosting->department }} — {{ $application->jobPosting->location }})</p>
    <p><strong>Applicant:</strong> {{ $application->name }}</p>
    <p><strong>Email:</strong> {{ $application->email }}</p>
    @if ($application->phone)
        <p><strong>Phone:</strong> {{ $application->phone }}</p>
    @endif
    @if ($application->cover_note)
        <p><strong>Cover note:</strong></p>
        <p>{{ $application->cover_note }}</p>
    @endif
    <p>View and manage this application, and download the CV, from the admin dashboard.</p>
</body>
</html>
