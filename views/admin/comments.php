<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /index.php?page=login');
    exit;
}

$comments = $viewData['comments'] ?? [];
?>

<div class="admin-container">
    <div class="admin-tabs">
        <a href="/index.php?page=admin" class="tab <?= !isset($_GET['tab']) ? 'active' : '' ?>">Recipe Submissions</a>
        <a href="/index.php?page=admin&tab=comments" class="tab <?= isset($_GET['tab']) && $_GET['tab'] === 'comments' ? 'active' : '' ?>">Comments</a>
        <a href="/index.php?page=contact" class="tab">Contact</a>
    </div>
    
    <h1>Comments Management</h1>
    
    <?php if (isset($viewData['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($viewData['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($viewData['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($viewData['error']) ?>
        </div>
    <?php endif; ?>

    <div class="comments-grid">
        <?php if (empty($comments)): ?>
            <p>No comments found.</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-card">
                    <div class="comment-header">
                        <h3>Comment on: <?= htmlspecialchars($comment['recipe_name']) ?></h3>
                        <span class="comment-meta">
                            By: <?= htmlspecialchars($comment['username'] ?? 'Anonymous') ?>
                            on <?= date('F j, Y g:i a', strtotime($comment['created_at'])) ?>
                        </span>
                    </div>
                    
                    <div class="comment-content">
                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                    </div>

                    <div class="comment-actions">
                        <form action="/index.php?page=admin" method="POST" class="delete-form">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <input type="hidden" name="action" value="delete_comment">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">Delete Comment</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.admin-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid #ddd;
    padding-bottom: 1rem;
}

.tab {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #666;
    border-radius: 4px;
}

.tab:hover {
    background-color: #f5f5f5;
}

.tab.active {
    background-color: #007bff;
    color: white;
}

.comments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.comment-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.comment-header {
    margin-bottom: 1rem;
}

.comment-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #333;
}

.comment-meta {
    font-size: 0.9rem;
    color: #666;
}

.comment-content {
    margin-bottom: 1rem;
    line-height: 1.5;
}

.comment-actions {
    display: flex;
    justify-content: flex-end;
}

.delete-form {
    margin: 0;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
}

.btn-danger:hover {
    background-color: #c82333;
}
</style> 