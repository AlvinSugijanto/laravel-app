<div>
    <h2>Welcome to Our Website, {{ $data->username }}</h2>
    <p>
        Click <a href="{{ url('api/verify_email/'. $token) }}">here</a> to verify your email.

    </p>
</div>