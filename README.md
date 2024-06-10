# Medical Consultation Management System

This is a medical Consultation management system built using Flask for the backend API and MySQL as the database. The system allows you to add, edit, and delete patients information, get medical records for patients and ensure data consistency using foreign key constraints. It also uses RabbitMQ for message queuing.

1. Clone the repository and place it in xampp > htdocs
2. Set up a virtual environment
3. Install the required Python packages, docker, mysql, and rabbitmq
4. Start MySQL and create the necessary databases and tables
5. In the terminal/CMD, type and enter: 
    ```bash
    python app.py
    ```
    The Flask server should be running at http://localhost:5004
6. Make a new terminal and start the Flask application: 
    ```bash
    python RekamanMedisApp/RekamanMedis.py
    ```
    The Flask server should be running at http://127.0.0.1:4000
7. Make a new terminal and start the Node.js application:
    ```bash
    node MedConsultationApp/app.js
    ```
    The Node.js server should be running at http://localhost:3000.

## Message Queue

The application uses RabbitMQ to publish messages when a new medical record is added. The RabbitMQ server should be running at `localhost`, and the queue name is `medical_records_queue`.
