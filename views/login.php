<?php if (isset($viewData['error'])): ?>
    <div class="error-message" role="alert"><?= htmlspecialchars($viewData['error']) ?></div>
<?php endif; ?>

<?php if (isset($viewData['success'])): ?>
    <div class="success-message" role="alert"><?= htmlspecialchars($viewData['success']) ?></div>
<?php endif; ?>

<div class="auth-container">
    <h1>Login</h1>
    
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
                   maxlength="255"
                   aria-required="true">
        </div>

        <button type="submit" name="login">Login</button>
    </form>

    <p class="auth-links">
        Don't have an account? <a href="?page=register">Register here</a>
    </p>
</div> 