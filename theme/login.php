<div class="zm-tt-form-container">
    <?php  // wp_login_form( array( 'redirect' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ); ?>
    <form action="javascript://" id="login_form">
        <div class="form-wrapper">
        <input type="hidden" value="task" name="post_type" />
        <p>
            <label>User Name</label>
            <input type="text" name="user_name" id="user_name" />
        </p>
        <p>
            <label>Password</label>
            <input type="password" name="password" id="password" />
        </p>
        <p>
            <label>Remember</label>
            <input type="checkbox" name="remember" id="remember" />
        </p>
        </div>

        <div class="button-container">
            <input id="login_button" class="button" type="submit" value="Submit" accesskey="p" name="submit" />
            <input id="login_exit" class="button" type="submit" value="Exit" name="exit" />
        </div>
    </form>
</div>
