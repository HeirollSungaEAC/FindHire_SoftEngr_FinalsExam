<?php
session_start();
include_once 'models.php';
include_once 'handleForms.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['user']['role'];

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $receiverId = $_POST['receiver_id'];
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        sendMessage($userId, $receiverId, $message);
    }
}

$selectedReceiverId = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : null;
$conversations = getConversations($userId);
$messages = [];
if ($selectedReceiverId) {
    $messages = getMessagesBetweenUsers($userId, $selectedReceiverId);
    $selectedUser = getUserById($selectedReceiverId);
}

$hrUsers = [];
if ($userRole == 'Applicant') {
    $hrUsers = getAllHRUsers();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .messages-container {
            display: flex;
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            min-height: calc(100vh - 100px);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .conversations-list {
            flex: 1;
            max-width: 300px;
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .messages-area {
            flex: 2;
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .message-box {
            padding: 1rem;
            margin: 0.8rem 0;
            border-radius: 12px;
            position: relative;
            max-width: 80%;
        }
        
        .message-form {
            margin-top: 2rem;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
        }
        
        .message-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
            resize: none;
        }
        
        .message-input:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .conversation-item {
            padding: 1rem;
            margin: 0.8rem 0;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .conversation-item:hover {
            background: #e9ecef;
            border-left-color: #007bff;
            transform: translateX(5px);
        }
        
        .sent-message {
            background: #007bff;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        
        .received-message {
            background: #f0f2f5;
            color: #1c1e21;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }
        
        .messages-list {
            height: 60vh;
            overflow-y: auto;
            padding: 1rem;
            background: #fff;
            border-radius: 12px;
        }
        
        .timestamp {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.5rem;
        }
        
        .section-title {
            color: #2c3e50;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .btn-send {
            background: #007bff;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-send:hover {
            background: #0056b3;
        }
        
        .no-messages {
            text-align: center;
            color: #6c757d;
            padding: 2rem;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header class="view-job-header">
        <div class="header-content">
            <h1 class="page-title">Messages</h1>
            <nav class="header-nav">
                <a href="<?php echo ($userRole == 'Applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>" class="nav-link">Dashboard</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </nav>
        </div>
    </header>

    <div class="messages-container">
        <div class="conversations-list">
            <h2 class="section-title">Conversations</h2>
            <?php if ($userRole == 'Applicant' && !empty($hrUsers)): ?>
                <h3 class="section-title">HR Representatives</h3>
                <?php foreach ($hrUsers as $hr): ?>
                    <div class="conversation-item" onclick="window.location.href='message.php?receiver_id=<?= $hr['id'] ?>'">
                        <?= htmlspecialchars($hr['username']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($conversations)): ?>
                <h3 class="section-title">Recent Chats</h3>
                <?php foreach ($conversations as $conversation): ?>
                    <div class="conversation-item" onclick="window.location.href='message.php?receiver_id=<?= $conversation['id'] ?>'">
                        <?= htmlspecialchars($conversation['username']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="messages-area">
            <?php if ($selectedReceiverId): ?>
                <h2 class="section-title">Chat with <?= htmlspecialchars($selectedUser['username']) ?></h2>
                <div class="messages-list">
                    <?php if (empty($messages)): ?>
                        <div class="no-messages">No messages yet. Start the conversation!</div>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-box <?= $message['sender_id'] == $userId ? 'sent-message' : 'received-message' ?>">
                                <p><?= htmlspecialchars($message['message']) ?></p>
                                <div class="timestamp"><?= date('M d, Y H:i', strtotime($message['timestamp'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form method="POST" class="message-form">
                    <input type="hidden" name="receiver_id" value="<?= $selectedReceiverId ?>">
                    <textarea name="message" class="message-input" rows="3" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn-send">Send Message</button>
                </form>
            <?php else: ?>
                <div class="no-messages">Select a conversation to start messaging</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        const messagesList = document.querySelector('.messages-list');
        if (messagesList) {
            messagesList.scrollTop = messagesList.scrollHeight;
        }
    </script>
</body>
</html>

