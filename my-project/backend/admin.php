<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .message-card {
            transition: all 0.3s ease;
        }
        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .unread {
            border-left: 4px solid #0d6efd;
            background-color: #f8f9fa;
        }
        .read {
            border-left: 4px solid #6c757d;
        }
        .btn-sm-custom {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Contact Messages Dashboard</h1>
                    <div>
                        <button class="btn btn-outline-primary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <a href="../contact.html" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Site
                        </a>
                    </div>
                </div>
                
                <?php
                require_once 'config.php';
                
                // Handle mark as read/unread actions
                if ($_POST['action'] ?? '' === 'toggle_read' && isset($_POST['message_id'])) {
                    $pdo = getDBConnection();
                    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = NOT is_read WHERE id = ?");
                    $stmt->execute([$_POST['message_id']]);
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
                
                // Handle delete action
                if ($_POST['action'] ?? '' === 'delete' && isset($_POST['message_id'])) {
                    $pdo = getDBConnection();
                    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                    $stmt->execute([$_POST['message_id']]);
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
                
                // Get messages from database
                $pdo = getDBConnection();
                if (!$pdo) {
                    echo '<div class="alert alert-danger">Database connection failed!</div>';
                    exit;
                }
                
                // Get statistics
                $stats_stmt = $pdo->query("
                    SELECT 
                        COUNT(*) as total_messages,
                        SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages,
                        SUM(CASE WHEN submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as this_week
                    FROM contact_messages
                ");
                $stats = $stats_stmt->fetch();
                
                // Get messages with pagination
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $per_page = 10;
                $offset = ($page - 1) * $per_page;
                
                $stmt = $pdo->prepare("
                    SELECT * FROM contact_messages 
                    ORDER BY submitted_at DESC 
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute([$per_page, $offset]);
                $messages = $stmt->fetchAll();
                
                // Get total count for pagination
                $count_stmt = $pdo->query("SELECT COUNT(*) as total FROM contact_messages");
                $total_messages = $count_stmt->fetch()['total'];
                $total_pages = ceil($total_messages / $per_page);
                ?>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Total Messages</h5>
                                        <h2 class="mb-0"><?php echo $stats['total_messages']; ?></h2>
                                    </div>
                                    <i class="bi bi-envelope-fill fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Unread Messages</h5>
                                        <h2 class="mb-0"><?php echo $stats['unread_messages']; ?></h2>
                                    </div>
                                    <i class="bi bi-envelope-exclamation-fill fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">This Week</h5>
                                        <h2 class="mb-0"><?php echo $stats['this_week']; ?></h2>
                                    </div>
                                    <i class="bi bi-calendar-week-fill fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Messages List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Messages</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
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
                                                    <!-- Mark as Read/Unread Button -->
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="toggle_read">
                                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                        <button type="submit" class="btn btn-sm-custom w-100 <?php echo $message['is_read'] ? 'btn-outline-secondary' : 'btn-outline-primary'; ?>">
                                                            <i class="bi <?php echo $message['is_read'] ? 'bi-envelope-open' : 'bi-envelope-check'; ?>"></i>
                                                            <?php echo $message['is_read'] ? 'Mark Unread' : 'Mark Read'; ?>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Reply Button (opens email client) -->
                                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo urlencode($message['subject']); ?>" 
                                                       class="btn btn-sm-custom btn-outline-success w-100">
                                                        <i class="bi bi-reply-fill"></i> Reply
                                                    </a>
                                                    
                                                    <!-- Delete Button -->
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                        <button type="submit" class="btn btn-sm-custom btn-outline-danger w-100">
                                                            <i class="bi bi-trash3-fill"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                                
                                                <!-- Additional Info -->
                                                <div class="mt-3 small text-muted">
                                                    <p class="mb-1">IP: <?php echo htmlspecialchars($message['ip_address']); ?></p>
                                                    <p class="mb-0">ID: #<?php echo $message['id']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Messages pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
