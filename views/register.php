<?php if (isset($viewData['error'])): ?>
    <div class="error-message" role="alert"><?= htmlspecialchars($viewData['error']) ?></div>
<?php endif; ?>

<?php if (isset($viewData['success'])): ?>
    <div class="success-message" role="alert"><?= htmlspecialchars($viewData['success']) ?></div>
<?php endif; ?>

<div class="auth-container">
    <h1>Register</h1>
    
    <form method="POST" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   required 
                   maxlength="255"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   aria-required="true">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required 
                   minlength="8"
                   maxlength="255"
                   aria-required="true">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" 
                   id="confirm_password" 
                   name="confirm_password" 
                   required 
                   minlength="8"
                   maxlength="255"
                   aria-required="true">
        </div>

        <button type="submit" name="register">Register</button>
    </form>

    <p class="auth-links">
        Already have an account? <a href="?page=login">Login here</a>
    </p>
</div> 