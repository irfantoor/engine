{include ../header.php}

<section id="login" class="w-100">
  <div class="container">
    <div class="row">
        <div class="col-lg-6">
            <h1>Forgot Password</h1>
            <div n:if="$message" class="alert alert-{$status}">
                {$message}
            </div>
            <form action="" method="post">

                <div class="form-group row">
                    <label for="subject" class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mb-2">Submit</button>

                <br>
                <a class="btn btn-light text-muted" href="/admin/login">Login</a>
                <a class="btn btn-light text-muted" href="/admin/register">Register</a>

            </form>
        </div>
    </div>
  </div>

</section>

<script>document.getElementById('email').focus();</script>

{include ../footer.php}
