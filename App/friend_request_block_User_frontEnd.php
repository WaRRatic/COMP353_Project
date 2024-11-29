<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbox</title>
    <link rel="stylesheet" type="text/css" href="../css/chatbox.css">
</head>
<body>
    <button onclick = "sendFriendRequest(<?php echo $other_user_id; ?>)">Send Friend Request </button>
    <button onclick = "blockUser(<?php echo $other_user_id; ?>)"> Block User </button>

    <script>

        function blockUser(blockedId) 
        {
            fetch('block_user.php', 
            {
                method: 'POST',
                headers: 
                {
                    'Content-Type': 'application/x-www-form-urlencoded' // This is used to format the sent data
                },
                body: 'blocked_id=' + blockedId
            }).then(response => response.text()).then(data => 
            {
                alert(data);
            })
        }

        function SendFriendRequest(receiverId) 
        {
            fetch('send_friend_request.php', 
            {
                method: 'POST',
                headers: 
                {
                    'Content-Type': 'application/x-www-form-urlencoded' // This is used to format the sent data
                },
                body: 'receiver_id=' + receiverId
            }).then(response => response.text()).then(data => 
            {
                alert(data);
            })
        }
    </script>
</body>

