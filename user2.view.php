<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat | User 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Chatbox [User: 2]</h5>
                </div>
                <div class="card-body">
                    <div class="chat-box" style="height: 300px; overflow-y: auto;" id="chatBox">
                        <!-- Chat messages will appear here -->
                    </div>
                </div>
                <div class="card-footer">
                    <form id="form">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type your message..." id="userInput" onkeypress="event.key === 'Enter' ? document.getElementById('sendButton').click() : ''">
                            <button class="btn btn-primary" type="submit" id="sendButton">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/stomp.js/2.3.3/stomp.min.js"></script>

<script>
    document.getElementById('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('userInput');
        const chatBox = document.getElementById('chatBox');

        // TODO API request to send message to the sender of user2
        if (input.value.trim()) {
            $.post('exchange.php', { message: input.value.trim(), username: 'user2' }, function(response) {
                console.log(response);
            });
        }

        input.value = '';
    });

    function renderUserMessage(message) {
        const userMessage = document.createElement('div');
        userMessage.classList.add('text-end', 'mb-2');
        userMessage.innerHTML = `<span class="badge bg-primary">Me: ${message}</span>`;
        chatBox.appendChild(userMessage);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function renderOtherMessage(message) {
        const botMessage = document.createElement('div');
        botMessage.classList.add('text-start', 'mb-2');
        botMessage.innerHTML = `<span class="badge bg-secondary">Other: ${message}</span>`;
        chatBox.appendChild(botMessage);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Create a WebSocket connection to RabbitMQ Web STOMP endpoint
    const socket = new WebSocket('ws://localhost:15674/ws'); // Change to your RabbitMQ Web STOMP URL
    const client = Stomp.over(socket);
    
    // Connect to RabbitMQ
    client.connect('guest', 'guest', (frame) => {
        console.log('Connected: ' + frame);

        // Subscribe to a queue (or a topic)
        client.subscribe('/queue/user2', (message) => {
            var data = JSON.parse(message.body);
            console.log('Received message [' + data.username + ']: ' + data.message);
            // Display the message in the HTML
            if (data.username == 'user2') {
                renderUserMessage(data.message);
            } else {
                renderOtherMessage(data.message);
            }
        });
    }, (error) => {
        console.error('Error connecting to RabbitMQ: ' + error);
    });
</script>

</body>
</html>
