<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Store user name in session when submitted with a comment
if (isset($_POST['user_name']) && !empty($_POST['user_name'])) {
    $_SESSION['user_name'] = $_POST['user_name'];
}
?>

<?php if (isset($viewData['error'])): ?>
    <div class="error-message" role="alert"><?= htmlspecialchars($viewData['error']) ?></div>
<?php endif; ?>

<?php if (isset($viewData['recipe'])): ?>
    <div class="recipe-container" itemscope itemtype="https://schema.org/Recipe">
        <?php if (!empty($viewData['recipe']['image_url'])): ?>
            <img src="<?= htmlspecialchars($viewData['recipe']['image_url']) ?>"
                alt="<?= htmlspecialchars($viewData['recipe']['name']) ?>"
                class="recipe-hero-image"
                itemprop="image">
        <?php else: ?>
            <div class="recipe-hero-image image-placeholder" role="img" aria-label="Recipe image placeholder"></div>
        <?php endif; ?>

        <article>
            <h1 itemprop="name"><?= htmlspecialchars($viewData['recipe']['name']) ?></h1>

            <div class="recipe-meta">
                <span class="category" itemprop="recipeCategory">
                    <?= htmlspecialchars($viewData['recipe']['category'] ?? 'Uncategorized') ?>
                </span>
                <time datetime="<?= date('c', strtotime($viewData['recipe']['created_at'])) ?>" itemprop="datePublished">
                    <?= date('M j, Y', strtotime($viewData['recipe']['created_at'])) ?>
                </time>
            </div>

            <div class="recipe-content">
                <section class="ingredients" aria-labelledby="ingredients-heading">
                    <h2 id="ingredients-heading">Ingredients</h2>
                    <div class="content" itemprop="recipeIngredient">
                        <?= nl2br(htmlspecialchars($viewData['recipe']['ingredients'])) ?>
                    </div>
                </section>

                <section class="instructions" aria-labelledby="instructions-heading">
                    <h2 id="instructions-heading">Instructions</h2>
                    <div class="content" itemprop="recipeInstructions">
                        <?= nl2br(htmlspecialchars($viewData['recipe']['instructions'])) ?>
                    </div>
                </section>
            </div>
        </article>

        <section class="comments-section" aria-labelledby="comments-heading">
            <h2 id="comments-heading">Comments (<?= count($viewData['comments'] ?? []) ?>)</h2>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" class="comment-form" aria-label="Add a comment">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="form-group">
                        <label for="content">Comment:</label>
                        <textarea id="content"
                                  name="content" 
                                  rows="4" 
                                  maxlength="1000"
                                  aria-required="true"
                                  required></textarea>
                    </div>

                    <button type="submit" name="comment">Post Comment</button>
                </form>
            <?php else: ?>
                <p class="login-prompt">Please <a href="?page=login">log in</a> to leave a comment.</p>
            <?php endif; ?>

            <?php if (!empty($viewData['comments'])): ?>
                <div class="comments-list" role="list">
                    <?php foreach ($viewData['comments'] as $comment): ?>
                        <div class="comment-card" role="listitem">
                            <div class="comment-header">
                                <span class="author"><?= htmlspecialchars($comment['username']) ?></span>
                                <time datetime="<?= date('c', strtotime($comment['created_at'])) ?>">
                                    <?= date('M j, Y g:i a', strtotime($comment['created_at'])) ?>
                                </time>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                <form method="POST" 
                                      class="delete-form"
                                      onsubmit="return confirm('Are you sure you want to delete this comment?')"
                                      style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                    <button type="submit" 
                                            name="delete_comment"
                                            class="delete-button"
                                            aria-label="Delete comment">
                                        <span class="delete-icon">×</span>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>

                            <p class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>

                            <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" 
                                  class="like-form"
                                  action="?page=recipe&id=<?= $viewData['recipe']['id'] ?>&action=like_comment&comment_id=<?= $comment['id'] ?>"
                                  aria-label="Like comment">

                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                <button type="submit" 
                                        aria-label="Like this comment"
                                        class="like-button <?= isset($comment['liked_by']) && strpos($comment['liked_by'], $_SESSION['user_id']) !== false ? 'liked' : '' ?>">
                                    <span class="heart-icon" aria-hidden="true">❤️</span>
                                    <span class="like-count"><?= htmlspecialchars($comment['like_count'] ?? 0) ?></span>
                                </button>
                            </form>
                            <?php else: ?>
                            <p class="login-prompt">Please <a href="?page=login">log in</a> to like comments.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-comments">No comments yet. Be the first to share your thoughts!</p>
            <?php endif; ?>
        </section>
    </div>
<?php else: ?>
    <?php include('../views/404.php') ?>
<?php endif; ?>

<style>
.recipe-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.recipe-content {
    display: flex;
    gap: 40px;
    margin-bottom: 40px;
}

.recipe-image {
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.recipe-details {
    flex: 1;
}

.comments-section {
    margin-top: 40px;
}

.comment-form {
    margin-bottom: 30px;
}

.comment-form textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
    font-family: inherit;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.comment {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #eee;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.comment-date {
    color: #666;
    font-size: 0.9em;
}

.comment-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
    align-items: center;
}

.like-button, .like-count {
    color: #666;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.like-button.liked {
    color: #ff4b4b;
}

.delete-button {
    background: none;
    border: none;
    color: #e74c3c;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    font-size: 1.2rem;
    border-radius: 4px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.2);
}

.delete-button:hover {
    background: rgba(231, 76, 60, 0.2);
    transform: translateY(-1px);
}

.delete-icon {
    font-weight: bold;
    line-height: 1;
}

.login-prompt {
    text-align: center;
    padding: 20px;
    background: #f5f5f5;
    border-radius: 8px;
    margin-bottom: 30px;
}

.login-prompt a {
    color: #007bff;
    text-decoration: none;
}

.login-prompt a:hover {
    text-decoration: underline;
}

.error-message {
    background: #ffe6e6;
    color: #d63031;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #ffb8b8;
}
</style>