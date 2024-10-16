# Chat Application Using RabbitMQ

## BUG that I faced today 16/10/2024

- I got [2 queues] => `user1`, `user2`
    - `user1.view.php`    ===> consumes the two queues [ and this is my mistake ]
    - `user2.view.php`    ===> consumes the two queues [ and this is my mistake ]
    - `console.view.php`  ===> consumes the two queues [ and this is my mistake ]
- Every file of them should have their queue separate from others
    - `user1.view.php`    ===> consumes `user1` queue [ correct ]
    - `user2.view.php`    ===> consumes `user2` queue [ correct ]
    - `console.view.php`  ===> consumes `console` queue [ another queue ]
- In the `exchange.php` - I made a declaration for both queues and publish the messages to them

## In the previous approach:
- We need to define a queue for each new consumer
- For example - I decide to extend my consumers and make a new consumer called `console`
- To make this consumer, I declared a new queue called console in the `exchange.php` and `console.view.php`
- I made this to make the console [my new consumer] listen from exchanging as well


