<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Admin</title>
    {{-- CSS --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="container-sm mt-5 p-5">
        <form method="POST" action="{{ route('admin.register.submit') }}">
            @csrf
            <div class="row justify-content-center">
                <div class="p-5 bg-light rounded-3 col-xl-4 shadow-lg">
                    <div class="mb-5 text-center">
                        <i class="bi-hexagon-fill fs-1 text-primary"></i>
                        <h4>Register Admin</h4>
                    </div>

                    <hr>

                    <div class="col mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="Email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <small>{{ $message }}</small>
                            </span>
                        @enderror
                    </div>

                    <div class="col mb-3">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password" required
                            autocomplete="new-password" placeholder="Password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <small>{{ $message }}</small>
                            </span>
                        @enderror
                    </div>

                    <div class="col mb-3">
                        <input id="password-confirm" type="password" class="form-control"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Confirm Password">
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col d-grid">
                            <button type="submit" class="btn btn-primary btn-lg mt-3"><i
                                    class="bi-box-arrow-in-right me-2"></i>Register</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
