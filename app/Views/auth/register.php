{include ../header.php}

<section id="login" class="w-100">
  <div class="container">
    <div class="row">
        <div class="col-lg-6">
            <h1 class="header">Register</h1>
            <div n:if="$message" class="alert alert-{$status}">
                {$message}
            </div>
            <form action="" method="post">

                <div class="form-group row">
                    <label for="name" class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="subject" class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mb-2">Submit</button>

                <br>
                <a class="btn btn-light text-muted" href="/admin/login">Login</a>
                <a class="btn btn-light text-muted" href="/admin/forgot">Forgot password</a>

            </form>
        </div>
    </div>
  </div>

</section>

<script>document.getElementById('name').focus();</script>

{include ../footer.php}
