<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #1f2937; line-height: 1.5;">
    <h2>Thanks for applying, {{ $application->name }}!</h2>
    <p>We've received your application for <strong>{{ $application->jobPosting->title }}</strong> at Green Art Factory.</p>
    <p>Our team will review your application and get back to you if there's a match.</p>
    <p>— Green Art Factory</p>
</body>
</html>
