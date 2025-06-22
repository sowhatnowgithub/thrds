# Thrds (spelled threads)- Real-time Messaging with PHP and OpenSwoole

**I just realised i am going to add an AI System to chat back, to feels less lonely with personality.**

**Thrds** is a real-time messaging platform built using **PHP**, **OpenSwoole**, and **WebSocket** for fast and efficient communication between users. With the ability to create threaded discussions, broadcast messages to all connected clients, and store messages persistently in a MySQL database, this system is designed to handle high-performance real-time communication at scale.

## Key Features
- **Threaded Discussions**: Create a thread with title, and let messaging begin in the thrds.
- **Real-time Broadcasting**: Messages are broadcasted to all connected users in real-time using WebSocket communication.
- **Openswoole Integration**: Leverages **OpenSwoole** for fast, asynchronous WebSocket communication, allowing PHP to handle multiple simultaneous connections efficiently.
- **Worker Process Communication**: Utilizes OpenSwoole's tables for sharing data between worker processes, ensuring efficient message broadcasting and scalability.
- **Persistent Message Storage**: A task manager that stores messages in a MySQL database (InnoDB engine) for durability, even in the case of system restarts.
* Currently there is no authorisation process, for the users, so anyone with the link can message.
* There is also an issue with the windows os, there is a bug in the css.(checkout the thrds.css in js)



## Future Developments

- **Make an AI system to chat back, caues i know only i will use it😂**
- **Create Authorisation Process for community trusted messaging**
- **Make the Styling and UI better**
- **Planning to host it**
- **Add Commenting and reactions**
## Architecture Overview

### Frontend
The frontend communicates with the backend via **WebSocket** using a JavaScript client. Messages are sent and received in real-time, ensuring that all users connected to the server stay in sync with the latest conversations.

### Backend
The backend is powered by **PHP** with **OpenSwoole**. OpenSwoole handles all WebSocket connections and real-time communication. Worker processes in OpenSwoole broadcast messages to all connected clients and handle interactions across threads.

### Database
Messages and threads are stored in a **MySQL database** using the **InnoDB engine**. A **task manager** handles the storage of messages in a non-transactional way, making the process efficient while ensuring data consistency.

### Workflow
1. **Clients (Frontend)**: Users connect to the server via WebSocket, join threads, and send/receive messages.
2. **Server (Backend)**: The PHP OpenSwoole server listens for incoming WebSocket messages, processes them, and broadcasts them to other connected users.
3. **Worker Processes**: OpenSwoole worker processes communicate via shared memory tables to broadcast messages efficiently.
4. **Database**: Messages are saved to the MySQL database using a task manager that ensures persistence.

## License

This project is licensed under the apache 2.0 License.


