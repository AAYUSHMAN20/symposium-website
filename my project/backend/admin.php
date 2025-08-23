<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .message-card { transition: all 0.3s ease; }
        .message-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .unread { border-left: 4px solid #0d6efd; background-color: #f8f9fa; }
        .read { border-left: 4px solid #6c757d; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Contact Messages Dashboard</h1>
                    <div>
                        <button class="btn btn-outline-primary" onclick="location.reload()">Refresh</button>
                        <a href="../contact.html" class="btn btn-secondary">Back to Site</a>
                    </div>
                </div>
                
                <?php
                require_once 'config.php';
                
                if (isset($_POST['action']) && $_POST['action'] === 'toggle_read' && isset($_POST['message_id'])) {
                    $pdo = getDBConnection();
                    if ($pdo) {
                        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = NOT is_read WHERE id = ?");
                        $stmt->execute([$_POST['message_id']]);
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit;
                    }
                }
                
                if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['message_id'])) {
                    $pdo = getDBConnection();
                    if ($pdo) {
                        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                        $stmt->execute([$_POST['message_id']]);
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit;
                    }
                }
                
                $pdo = getDBConnection();
                if (!$pdo) {
                    echo '<div class="alert alert-danger">Database connection failed!</div>';
                    exit;
                }
                
                $stats_stmt = $pdo->query("SELECT COUNT(*) as total_messages, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages FROM contact_messages");
                $stats = $stats_stmt->fetch();
                
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $per_page = 10;
                $offset = ($page - 1) * $per_page;
                
                $stmt = $pdo->prepare("SELECT * FROM contact_messages ORDER BY submitted_at DESC LIMIT ? OFFSET ?");
                $stmt->execute([$per_page, $offset]);
                $messages = $stmt->fetchAll();
                
                $count_stmt = $pdo->query("SELECT COUNT(*) as total FROM contact_messages");
                $total_messages = $count_stmt->fetch()['total'];
                $total_pages = ceil($total_messages / $per_page);
                ?>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Messages</h5>
                                <h2 class="mb-0"><?php echo $stats['total_messages']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Unread Messages</h5>
                                <h2 class="mb-0"><?php echo $stats['unread_messages']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Messages</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted mt-2">No messages found.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message-card card mb-3 <?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0">
                                                        <?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?>
                                                        <?php if (!$message['is_read']): ?>
                                                            <span class="badge bg-primary">New</span>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y g:i A', strtotime($message['submitted_at'])); ?>
                                                    </small>
                                                </div>
                                                <p class="mb-1"><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                                                <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                                                <?php if ($message['phone']): ?>
                                                    <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($message['phone']); ?></p>
                                                <?php endif; ?>
                                                <p class="mb-2"><strong>Message:</strong></p>
                                                <div class="border-start border-3 ps-3 bg-light p-2">
                                                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex flex-column gap-2">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="toggle_read">
                                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                        <button type="submit" class="btn btn-sm w-100 <?php echo $message['is_read'] ? 'btn-outline-secondary' : 'btn-outline-primary'; ?>">
                                                            <?php echo $message['is_read'] ? 'Mark Unread' : 'Mark Read'; ?>
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo urlencode($message['subject']); ?>" 
                                                       class="btn btn-sm btn-outline-success w-100">Reply</a>
                                                    
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
