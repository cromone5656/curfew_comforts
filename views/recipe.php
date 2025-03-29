<?php if (isset($viewData['error'])): ?>
    <div class="error-message"><?= htmlspecialchars($viewData['error']) ?></div>
<?php endif; ?>

<?php if (isset($viewData['recipe'])): ?>
    <div class="recipe-container">
        <article>
            <h1><?= htmlspecialchars($viewData['recipe']['name']) ?></h1>

            <div class="recipe-meta">
                <span class="category">
                    <?= htmlspecialchars($viewData['recipe']['category'] ?? 'Uncategorized') ?>
                </span>
                <time datetime="<?= date('c', strtotime($viewData['recipe']['created_at'])) ?>">
                    <?= date('M j, Y', strtotime($viewData['recipe']['created_at'])) ?>
                </time>
            </div>

            <div class="recipe-content">
                <section class="ingredients">
                    <h2>Ingredients</h2>
                    <div class="content"><?= nl2br(htmlspecialchars($viewData['recipe']['ingredients'])) ?></div>
                </section>

                <section class="instructions">
                    <h2>Instructions</h2>
                    <div class="content"><?= nl2br(htmlspecialchars($viewData['recipe']['instructions'])) ?></div>
                </section>
            </div>
        </article>

        <section class="comments-section">
            <h2>Comments (<?= count($viewData['comments'] ?? []) ?>)</h2>

            <form method="POST" class="comment-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <label for="user_name">Name:</label>
                    <input type="text" name="user_name" required maxlength="100"
                        value="<?= htmlspecialchars($_POST['user_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="content">Comment:</label>
                    <textarea name="content" rows="4" required maxlength="1000"><?=
                                                                                htmlspecialchars($_POST['content'] ?? '')
                                                                                ?></textarea>
                </div>

                <button type="submit" name="comment">Post Comment</button>
            </form>

            <?php if (!empty($viewData['comments'])): ?>
                <div class="comments-list">
                    <?php foreach ($viewData['comments'] as $comment): ?>
                        <div class="comment-card">
                            <div class="comment-header">
                                <span class="author"><?= htmlspecialchars($comment['user_name']) ?></span>
                                <time datetime="<?= date('c', strtotime($comment['created_at'])) ?>">
                                    <?= date('M j, Y g:i a', strtotime($comment['created_at'])) ?>
                                </time>
                            </div>

                            <p class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>

                            <form method="POST" class="like-form"
                                action="?page=recipe&id=<?= $viewData['recipe']['id'] ?>&action=like_comment&comment_id=<?= $comment['id'] ?>">

                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="user_name" value="<?= htmlspecialchars($_POST['user_name'] ?? '') ?>">

                                <button type="submit" aria-label="Like this comment">
                                    ❤️ <?= htmlspecialchars($comment['like_count'] ?? 0) ?>
                                </button>
                            </form>
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