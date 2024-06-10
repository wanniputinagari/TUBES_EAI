const express = require('express');
const mysql = require('mysql2');
const path = require('path');
const amqp = require('amqplib/callback_api');
const app = express();

// Middleware to log requests
app.use((req, res, next) => {
    console.log(`${req.method} ${req.url} - ${JSON.stringify(req.body)}`);
    next();
});

app.use(express.json()); // Parse JSON bodies
app.use(express.urlencoded({ extended: true })); // Parse URL-encoded bodies

// Middleware to serve static files
app.use('/static', express.static(path.join(__dirname, 'static')));

// MySQL connection configuration
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'medical_consultation'
});

// Connect to MySQL
connection.connect(err => {
    if (err) {
        console.error('Error connecting to MySQL: ' + err.stack);
        return;
    }
    console.log('Connected to MySQL as id ' + connection.threadId);
});

// RabbitMQ setup
let channel = null;
const queue = 'medical_records_queue';

amqp.connect('amqp://localhost', (err, conn) => {
    if (err) {
        console.error('Error connecting to RabbitMQ:', err);
        return;
    }
    conn.createChannel((err, ch) => {
        if (err) {
            console.error('Error creating RabbitMQ channel:', err);
            return;
        }
        channel = ch;
        channel.assertQueue(queue, { durable: true });

        // Consume messages from the queue
        channel.consume(queue, (msg) => {
            if (msg !== null) {
                console.log(" [x] Received '%s'", msg.content.toString());
                const data = JSON.parse(msg.content.toString());
        
                // Process the message (e.g., insert into database)
                const { patient_id, doctor_id, visit_date, diagnosis, prescription } = data;
                const sql = 'INSERT INTO record_receiver (patient_id, doctor_id, visit_date, diagnosis, prescription) VALUES (?, ?, ?, ?, ?)';
        
                connection.query(sql, [patient_id, doctor_id, visit_date, diagnosis, prescription], (error, results) => {
                    if (error) {
                        console.error('Error executing MySQL query: ' + error.stack);
                        return;
                    }
                    console.log('Record added to database');
                });
                // Acknowledge the message
                channel.ack(msg);
            }
        });
    });
});

// Function to publish a message to RabbitMQ
const publishToQueue = async (message) => {
    if (!channel) {
        console.error('RabbitMQ channel not initialized');
        return;
    }
    channel.sendToQueue(queue, Buffer.from(message));
    console.log(" [x] Sent '%s'", message);
};

// Example of publishing a message when a new consultation is added
app.post('/consultations', (req, res) => {
    const { patient_id, doctor_name, date, notes } = req.body;
    const sql = 'INSERT INTO consultations (patient_id, doctor_name, date, notes) VALUES (?, ?, ?, ?)';

    connection.query(sql, [patient_id, doctor_name, date, notes], (error, results) => {
        if (error) {
            console.error('Error executing MySQL query: ' + error.stack);
            return res.status(500).json({ error: 'Database error' });
        }
        const message = JSON.stringify({ patient_id, doctor_name, date, notes });
        publishToQueue(message);
        res.json({ message: 'Consultation added successfully' });
    });
});

// Get all consultations
app.get('/consultations', (req, res) => {
    const sql = 'SELECT * FROM consultations';

    connection.query(sql, (error, results, fields) => {
        if (error) {
            console.error('Error executing MySQL query: ' + error.stack);
            return res.status(500).json({ error: 'Database error' });
        }
        res.json(results);
    });
});

// Get all doctors
app.get('/doctors', (req, res) => {
    const sql = 'SELECT * FROM doctors';

    connection.query(sql, (error, results, fields) => {
        if (error) {
            console.error('Error executing MySQL query: ' + error.stack);
            return res.status(500).json({ error: 'Database error' });
        }
        res.json(results);
    });
});

// Get all patients
app.get('/patients', (req, res) => {
    const sql = 'SELECT * FROM patients';

    connection.query(sql, (error, results, fields) => {
        if (error) {
            console.error('Error executing MySQL query: ' + error.stack);
            return res.status(500).json({ error: 'Database error' });
        }
        res.json(results);
    });
});

// Get a single patient by ID
app.get('/detailpatient/:id', (req, res) => {
    const id = req.params.id;
    const sql = 'SELECT * FROM patients WHERE patient_id = ?';

    connection.query(sql, [id], (error, results) => {
        if (error) {
            console.error('Error executing MySQL query:', error);
            return res.status(500).json({ error: 'Database error' });
        }

        if (results.length === 0) {
            return res.status(404).json({ error: 'Data not found' });
        }

        res.json(results[0]); // Return the first result (assuming ID is unique)
    });
});

// Get a single consultation by patient ID
app.get('/detailconsultation/:id', (req, res) => {
    const id = req.params.id;
    const sql = 'SELECT * FROM consultations WHERE patient_id = ?';

    connection.query(sql, [id], (error, results) => {
        if (error) {
            console.error('Error executing MySQL query:', error);
            return res.status(500).json({ error: 'Database error' });
        }

        if (results.length === 0) {
            return res.status(404).json({ error: 'Data not found' });
        }

        res.json(results[0]); // Return the first result (assuming ID is unique)
    });
});

// Create a new patient
app.post('/postpatients', (req, res) => {
    console.log(req.body);
    const { name, dob, gender, address, phone, email } = req.body;
    const sql = 'INSERT INTO patients (name, dob, gender, address, phone, email) VALUES (?, ?, ?, ?, ?, ?)';

    connection.query(sql, [name, dob, gender, address, phone, email], (error, results) => {
        if (error) {
            console.error('Error executing MySQL query: ' + error.stack);
            return res.status(500).json({ error: 'Database error' });
        }
        
        res.redirect('http://localhost:8080/UAS_EAI/index.php');
    });
});

// Update an existing patient by ID
app.put('/putpatients/:id', (req, res) => {
    const patient_id = req.params.id;
    const { name, dob, gender, address, phone, email } = req.body;
    const sql = 'UPDATE patients SET name = ?, dob = ?, gender = ?, address = ?, phone = ?, email = ? WHERE patient_id = ?';

    connection.query(sql, [name, dob, gender, address, phone, email, patient_id], (error, results) => {
        if (error) {
            console.error('Error executing MySQL query: ' + error.stack);
            return res.status(500).json({ error: 'Database error' });
        }
        if (results.affectedRows === 0) {
            return res.status(404).json({ error: 'Patient not found' });
        }
        res.redirect('http://localhost:8080/UAS_EAI/index.php');
    });
});

// Delete a patient by ID
app.delete('/delpatients/:id', (req, res) => {
    const patient_id = req.params.id;
    const sql = 'DELETE FROM patients WHERE patient_id = ?';

    connection.query(sql, [patient_id], (error, results) => {
        if (error) {
            console.error('Error executing MySQL query: ' + error.stack);
            return res.status(500).json({ error: 'Database error' });
        }
        if (results.affectedRows === 0) {
            return res.status(404).json({ error: 'Patient not found' });
        }
        res.json({ message: 'Patient deleted successfully' });
    });
});

// Global error handler for JSON syntax errors
app.use((err, req, res, next) => {
    if (err instanceof SyntaxError && err.status === 400 && 'body' in err) {
        console.error('Bad JSON:', err.message);
        return res.status(400).json({ error: 'Invalid JSON' });
    }
    next();
});

// Start the server
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});

