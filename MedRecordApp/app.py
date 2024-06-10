import pika
import json
from flask import Flask, jsonify, request
from flask_mysqldb import MySQL
from datetime import datetime

app = Flask(__name__)

# MySQL configuration
app.config['MYSQL_HOST'] = 'localhost'
app.config['MYSQL_USER'] = 'root'
app.config['MYSQL_PASSWORD'] = ''
app.config['MYSQL_DB'] = 'medical_record_management'

mysql = MySQL(app)

# RabbitMQ configuration
RABBITMQ_HOST = 'localhost'
QUEUE_NAME = 'medical_records_queue'

def generate_response(status_code, message, data=None):
    response = {'status_code': status_code, 'message': message, 'timestamp': datetime.now().isoformat()}
    if data:
        response['data'] = data
    return jsonify(response), status_code

def publish_message(message):
    try:
        connection = pika.BlockingConnection(pika.ConnectionParameters(RABBITMQ_HOST))
        channel = connection.channel()
        channel.queue_declare(queue=QUEUE_NAME, durable=True)
        channel.basic_publish(
            exchange='',
            routing_key=QUEUE_NAME,
            body=json.dumps(message),
            properties=pika.BasicProperties(
                delivery_mode=2,  # make message persistent
            ))
        connection.close()
    except Exception as e:
        return generate_response(500, f'Error publishing message to RabbitMQ: {str(e)}')

@app.route('/')
def root():
    return 'Welcome to Medical Records'

@app.route('/add_record', methods=['POST'])
def add_record():
    try:
        data = request.get_json()

        # Check if the patient_id exists in the patients table
        patient_id = data.get('patient_id')
        cursor = mysql.connection.cursor()
        cursor.execute("SELECT COUNT(*) FROM medical_consultation.patients WHERE patient_id = %s", (patient_id,))
        patient_exists = cursor.fetchone()[0]
        cursor.close()

        if not patient_exists:
            return generate_response(404, f'Patient with ID {patient_id} does not exist')

        # Check if the doctor_id exists in the doctors table
        doctor_id = data.get('doctor_id')
        cursor = mysql.connection.cursor()
        cursor.execute("SELECT COUNT(*) FROM medical_consultation.doctors WHERE doctor_id = %s", (doctor_id,))
        doctor_exists = cursor.fetchone()[0]
        cursor.close()

        if not doctor_exists:
            return generate_response(404, f'Doctor with ID {doctor_id} does not exist')

        # Insert record into medical_records table
        cursor = mysql.connection.cursor()
        sql = "INSERT INTO medical_records (patient_id, doctor_id, visit_date, diagnosis, prescription) VALUES (%s, %s, %s, %s, %s)"
        val = (data['patient_id'], data['doctor_id'], data['visit_date'], data['diagnosis'], data['prescription'])
        cursor.execute(sql, val)
        mysql.connection.commit()
        cursor.close()

        # Publish message to RabbitMQ
        message = {
            'patient_id': data['patient_id'],
            'doctor_id': data['doctor_id'],
            'visit_date': data['visit_date'],
            'diagnosis': data['diagnosis'],
            'prescription': data['prescription']
        }
        publish_message(message)

        return generate_response(201, 'Record added successfully')
    except Exception as e:
        return generate_response(500, f'Error processing request: {str(e)}')



@app.route('/medical_records', methods=['GET'])
def medical_records():
    if request.method == 'GET':
        # Get query parameters
        query_params = request.args.to_dict()

        # Constructing the query based on parameters
        query = "SELECT * FROM medical_records WHERE 1=1"
        params = []
        for key, value in query_params.items():
            query += f" AND {key} = %s"
            params.append(value)

        # Fetching data from the database
        cursor = mysql.connection.cursor()
        cursor.execute(query, tuple(params))
        column_names = [i[0] for i in cursor.description]
        data = [dict(zip(column_names, row)) for row in cursor.fetchall()]
        cursor.close()

        return generate_response(200, 'Medical records fetched successfully', data)
    
    else:
        return generate_response(400, 'data not provided')

@app.route('/detailrecords/')
def detailrecords():
    if 'id' in request.args:
        cursor = mysql.connection.cursor()
        sql = "SELECT * FROM medical_records WHERE patient_id = %s"
        val = (request.args['id'],)
        cursor.execute(sql, val)

        #get column names from cursor.decription
        column_names = [i[0] for i in cursor.description]

        #fetch data and format into list of dictionaries
        data = []
        for row in cursor.fetchall():
            data.append(dict(zip(column_names, row)))
            
        return jsonify(data)
        cursor.close()

@app.route('/update_record', methods=['PUT'])
def update_record():
    if 'id' in request.args:
        data = request.get_json()

        cursor = mysql.connection.cursor()
        sql = "UPDATE medical_records SET patient_id=%s, doctor_id=%s, visit_date=%s, diagnosis=%s, prescription=%s WHERE history_id=%s"
        val = (data['patient_id'], data['doctor_name'], data['visit_date'], data['diagnosis'], data['prescription'], request.args['id'])
        cursor.execute(sql, val)
        mysql.connection.commit()
        cursor.close()

        return generate_response(200, 'Record updated successfully')
    else:
        return generate_response(400, 'history ID not provided')

@app.route('/delete_record', methods=['DELETE'])
def delete_record():
    if 'id' in request.args:
        cursor = mysql.connection.cursor()
        sql = "DELETE FROM medical_records WHERE patient_id=%s"
        val = (request.args['id'],)
        cursor.execute(sql, val)
        mysql.connection.commit()
        cursor.close()

        return generate_response(200, 'Record deleted successfully')
    else:
        return generate_response(400, 'Patient ID is not provided')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=4000)
