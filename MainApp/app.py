from flask import Flask, render_template, request, redirect, url_for
import requests

app = Flask(__name__)

@app.route('/')
def landing():
    return render_template('index.html')


# Layanan Konsultasi
def get_patients(patient_id):
    response = requests.get(f'http://localhost:3000/detailpatient/{patient_id}')
    return response.json()

# Layanan Pasien
def get_records(patient_id):
    response = requests.get(f'http://localhost:4000/detailrecords?id={patient_id}')
    return response.json()

#INFORMASI LAYANAN
@app.route('/patients', methods=['GET'])
def get_fullpatients():
    # Get layanan konsultasi
    response = requests.get('http://localhost:3000/patients')
    patients_info = response.json()
    
    # Additional information
    info = {
        'name': 'Medical Service Name',
        'patients_info': 'Sample information about patients',
        'records_info': 'Sample data records details'
    }
    
    return render_template('index.html', patients_info=patients_info, info=info)

@app.route('/patientrecords/<int:patient_id>')
def get_patient_info(patient_id):
    # Get layanan konsultasi
    patients_info = get_patients(patient_id)
    records_info = get_records(patient_id)
    patient_data = {
        'name': patients_info['name'],
        'email': patients_info['email'],
        'dob': patients_info['dob'],
        'gender': patients_info['gender'],
        'address': patients_info['address'],
        'phone': patients_info['phone']
    }
    return render_template('index.html', info=patient_data, records=records_info)

# Route for adding a new patient
@app.route('/add_patient', methods=['GET', 'POST'])
def add_patient():
    if request.method == 'POST':
        # Get form data
        name = request.form['name']
        dob = request.form['dob']
        gender = request.form['gender']
        address = request.form['address']
        phone = request.form['phone']
        email = request.form['email']
        
        # Send the data to the backend service for adding the new patient
        response = requests.post('http://localhost:3000/postpatients', json={
            'name': name,
            'dob': dob,
            'gender': gender,
            'address': address,
            'phone': phone,
            'email': email
        })
        
        if response.status_code == 201:
            return redirect(url_for('index'))
        else:
            return "Failed to add patient"
    
    return render_template('add_patient.html')

# Route for updating a patient
@app.route('/putpatients', methods=['PUT'])
def put_patient():
    if request.method == 'POST':
        # Get form data
        name = request.form['name']
        dob = request.form['dob']
        gender = request.form['gender']
        address = request.form['address']
        phone = request.form['phone']
        email = request.form['email']
        
        # Send the data to the backend service for adding the new patient
        response = requests.post('http://localhost:3000/postpatients', json={
            'name': name,
            'dob': dob,
            'gender': gender,
            'address': address,
            'phone': phone,
            'email': email
        })
        
        if response.status_code == 201:
            return redirect(url_for('index'))
        else:
            return "Failed to add patient"
    
    return render_template('add_patient.html')

@app.route('/medical_records', methods=['GET'])
def med_history():
    return render_template('medical_records.php')

if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5004)
