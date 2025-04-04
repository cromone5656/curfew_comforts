<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /index.php?page=login');
    exit;
}

$submissions = $viewData['submissions'] ?? [];
?>

<div class="admin-container">
    <div class="admin-tabs">
        <a href="/index.php?page=admin" class="tab <?= !isset($_GET['tab']) ? 'active' : '' ?>">Recipe Submissions</a>
        <a href="/index.php?page=admin&tab=comments" class="tab <?= isset($_GET['tab']) && $_GET['tab'] === 'comments' ? 'active' : '' ?>">Comments</a>
        <a href="/index.php?page=contact" class="tab">Contact</a>
    </div>
    
    <h1>Recipe Submissions</h1>
    
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

    <div class="submissions-grid">
        <?php if (empty($submissions)): ?>
            <p>No recipe submissions found.</p>
        <?php else: ?>
            <?php foreach ($submissions as $submission): ?>
                <div class="submission-card">
                    <div class="submission-header">
                        <h2><?= htmlspecialchars($submission['recipe_name']) ?></h2>
                        <span class="status-badge status-<?= htmlspecialchars($submission['status']) ?>">
                            <?= ucfirst(htmlspecialchars($submission['status'])) ?>
                        </span>
                    </div>
                    
                    <div class="submission-meta">
                        <p><strong>Submitted by:</strong> <?= htmlspecialchars($submission['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($submission['email']) ?></p>
                        <p><strong>Category:</strong> <?= htmlspecialchars($submission['category']) ?></p>
                        <p><strong>Submitted on:</strong> <?= date('F j, Y', strtotime($submission['created_at'])) ?></p>
                    </div>

                    <div class="submission-content">
                        <h3>Ingredients</h3>
                        <div class="ingredients">
                            <?= nl2br(htmlspecialchars($submission['ingredients'])) ?>
                        </div>

                        <h3>Instructions</h3>
                        <div class="instructions">
                            <?= nl2br(htmlspecialchars($submission['instructions'])) ?>
                        </div>

                        <?php if ($submission['notes']): ?>
                            <h3>Additional Notes</h3>
                            <div class="notes">
                                <?= nl2br(htmlspecialchars($submission['notes'])) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($submission['image_url']): ?>
                            <div class="image-preview">
                                <img src="<?= htmlspecialchars($submission['image_url']) ?>" alt="Recipe image">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="submission-actions">
                        <form action="/index.php?page=admin" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                            <input type="hidden" name="action" value="update_status">
                            
                            <select name="status" class="status-select">
                                <option value="pending" <?= $submission['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $submission['status'] === 'approved' ? 'selected' : '' ?>>Approve</option>
                                <option value="rejected" <?= $submission['status'] === 'rejected' ? 'selected' : '' ?>>Reject</option>
                            </select>
                            
                            <button type="submit" class="btn btn-primary">Update Status</button>
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

/* Existing styles remain unchanged */
</style> 