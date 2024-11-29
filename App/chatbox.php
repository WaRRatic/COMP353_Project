<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbox</title>
    <link rel="stylesheet" type="text/css" href="../css/chatbox.css">
</head>
<body>
    <h1>Chatbox</h1>
    <div>
        <label for="friendSelect">Select a Friend:</label>
        <select id="friendSelect" onchange="updateChat()">
            <option value="" disabled selected>Select a friend</option>
        </select>
    </div>
    <div id="chatBox" class="chat-box"></div>
    <input type="text" id="messageInput" placeholder="Type your message">
    <button onclick="sendMessage()">Send</button>

    <script>
        const loggedInUserId = <?php echo $_SESSION['member_id']; ?>;

        // Fetch the list of friends
        function fetchFriends() {
            fetch('get_friends.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const friendSelect = document.getElementById('friendSelect');
                        data.friends.forEach(friend => {
                            const option = document.createElement('option');
                            option.value = friend.friend_id;
                            option.textContent = friend.friend_name;
                            friendSelect.appendChild(option);
                        });
                    } else {
                        alert('Failed to fetch friends.');
                    }
                });
        }

        // Fetch messages for the selected friend
        function fetchMessages(targetId) {
            fetch(`get_messages.php?origin_member_id=${loggedInUserId}&target_member_id=${targetId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const chatBox = document.getElementById('chatBox');
                        chatBox.innerHTML = data.messages.map(msg => `
                            <div class="${msg.origin_member_id == loggedInUserId ? 'sent' : 'received'}">
                                ${msg.message_content}
                            </div>
                        `).join('');
                    } else {
                        alert('Failed to fetch messages.');
                    }
                });
        }

        // Send a message to the selected friend
        function sendMessage() {
            const targetId = document.getElementById('friendSelect').value;
            const content = document.getElementById('messageInput').value;

            if (!targetId || !content.trim()) {
                alert('Please select a friend and type a message.');
                return;
            }

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    origin_member_id: loggedInUserId,
                    target_member_id: targetId,
                    message_content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('messageInput').value = ''; // Clear input
                    fetchMessages(targetId); // Refresh chat
                } else {
                    alert('Failed to send message.');
                }
            });
        }

        // Update chat when a new friend is selected
        function updateChat() {
            const targetId = document.getElementById('friendSelect').value;
            if (targetId) {
                fetchMessages(targetId);
            }
        }

        // Initialize the page
        fetchFriends();
    </script>
</body>
</html>