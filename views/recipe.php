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
                        <?= nl2br(htmlspecialchars($viewData['recipe']['ingredients'], ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                </section>

                <section class="instructions" aria-labelledby="instructions-heading">
                    <h2 id="instructions-heading">Instructions</h2>
                    <div class="content" itemprop="recipeInstructions">
                        <?= nl2br(htmlspecialchars($viewData['recipe']['instructions'], ENT_QUOTES, 'UTF-8')) ?>
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