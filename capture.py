import cv2
from pyzbar.pyzbar import decode
import serial
import requests
import csv
import json
import time
import datetime
import mysql.connector

delay_seconds = 0.1  # Set the delay time in seconds

def get_class_for_teacher(teacher_id):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()

    query = "SELECT ClassID FROM classes WHERE TeacherID = %s"
    cursor.execute(query, (teacher_id,))
    result = cursor.fetchall()  # Fetch all classes for the teacher
    conn.close()

    if result:
        class_ids = [row[0] for row in result]  # Extract the integer IDs from the tuples
        return class_ids
    else:
        print("No class found for TeacherID:", teacher_id)
        return None

def wait_for_teacher():
    print("Please scan a teacher's RFID to start the session.")
    while True:
        if arduino.in_waiting > 0:
            rfid_data = arduino.readline().decode('utf-8').rstrip()
            if "Card UID: " in rfid_data:
                rfid_data = rfid_data.replace("Card UID: ", "").strip()
                print("Scanned RFID:", rfid_data)
                if rfid_data in (t1, t2):
                    print("Teacher detected. Starting attendance capture.")
                    return rfid_data
                else:
                    print("Invalid RFID. Waiting for a teacher's RFID.")
            else:
                print("Waiting for a valid RFID scan...")

def lookup_student_id_by_rfid(rfid):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()

    query = "SELECT id FROM students WHERE rfid_card_number = %s"
    cursor.execute(query, (rfid,))
    result = cursor.fetchone()
    conn.close()

    if result:
        student_id = result[0]  # Extract the integer ID from the tuple
        return student_id
    else:
        print("No student found with RFID:", rfid)
        return None
    
def lookup_name_by_ID(id):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()

    query = "SELECT name FROM students WHERE id = %s"
    cursor.execute(query, (id,))
    result = cursor.fetchone()
    conn.close()

    if result:
        student_name = result[0]  # Extract the integer ID from the tuple
        return student_name

def lookup_teacher_id_by_ClassID(ClassID):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()

    query = "SELECT TeacherID FROM classes WHERE ClassID = %s"
    cursor.execute(query, (ClassID,))
    result = cursor.fetchone()
    conn.close()

    if result:
        teacher_id = result[0]  # Extract the integer ID from the tuple
        return teacher_id
    else:
        print("No teacher found for ClassID:", ClassID)
        return None    

def lookup_class_id_by_qr_code(qr_code):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()

    query = "SELECT ClassID FROM students WHERE qr_code = %s"
    cursor.execute(query, (qr_code,))
    result = cursor.fetchone()
    conn.close()

    if result:
        class_id = result[0]  # Extract the integer ID from the tuple
        return class_id
    else:
        print("No student found with QR code:", qr_code)
        return None

def lookup_class_id_by_rfid(rfid):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()

    query = "SELECT ClassID FROM students WHERE rfid_card_number = %s"
    cursor.execute(query, (rfid,))
    result = cursor.fetchone()
    conn.close()

    if result:
        class_id = result[0]  # Extract the integer ID from the tuple
        return class_id
    else:
        print("No student found with RFID:", rfid)
        return None

def lookup_teacher_id_by_rfid(rfid):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()
    query = "SELECT id FROM teachers WHERE rfid_card_number = %s"
    cursor.execute(query, (rfid,))
    result = cursor.fetchone()
    conn.close()

    if result:
        teacher_id = result[0]  # Extract the integer ID from the tuple
        return teacher_id
    else:
        print("No teacher found with RFID:", rfid)
        return None

def student_exists(student_id):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="school"
    )
    cursor = conn.cursor()
    query = "SELECT id FROM students WHERE id = %s"
    cursor.execute(query, (student_id,))
    result = cursor.fetchone()
    conn.close()
    return result is not None

def check_for_absences(scheduled_classes, actual_attendees):
    for scheduled_class in scheduled_classes:
        expected_attendees = {student['id'] for student in scheduled_class['students']}
        absentees = expected_attendees - set(actual_attendees)
        for absentee in absentees:
            print(f"Student ID {absentee} is absent for class {scheduled_class['class_id']}.")
            # Send absent attendance
            send_attendance(absentee, scheduled_class['class_id'], current_date, current_time, 'absent', teacher_id)

def generate_attendance_report(attendance_records):
    print("Attendance Report:")
    for record in attendance_records:
        print(record)

def export_attendance_data(attendance_data, filename):
    if attendance_data:  # Check if the list is not empty
        keys = attendance_data[0].keys()
        with open(filename, 'w', newline='') as output_file:
            dict_writer = csv.DictWriter(output_file, keys)
            dict_writer.writeheader()
            dict_writer.writerows(attendance_data)
    else:
        print("No attendance records to export.")

def send_attendance(StudentID, ClassID, Date, Time, Status, TeacherID, Remarks=None):
    if not student_exists(StudentID):
        print(f"Student ID {StudentID} does not exist in the students table. Skipping attendance record.")
        return

    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="school"
        )
        cursor = conn.cursor()
        sql = "INSERT INTO attendance (StudentID, ClassID, Date, Time, Status, TeacherID, Remarks) VALUES (%s, %s, %s, %s, %s, %s, %s)"
        values = (StudentID, ClassID, Date, Time, Status, TeacherID, Remarks)
        
        # Debugging output to check values
        print("Values to be inserted:", values)
        
        cursor.execute(sql, values)
        conn.commit()
    except mysql.connector.Error as e:
        print("Error inserting attendance data:", e)
    finally:
        conn.close()

current_date = datetime.datetime.now().strftime("%Y-%m-%d")
current_time = datetime.datetime.now().strftime("%H:%M:%S")

def get_teacher_rfid():
    try:
        response = requests.get("http://localhost/pyarduino/final%20csci490/get_teacher_rfid.php")
        return response.text.strip()
    except Exception as e:
        print("Failed to get teacher RFID:", e)
        return None

try:
    arduino = serial.Serial('COM4', 9600)
except Exception as e:
    print("Failed to connect to Arduino:", e)
    exit()

try:
    cap = cv2.VideoCapture(0)
except Exception as e:
    print("Failed to access camera:", e)
    exit()

start_time = None
timeout = 1 * 60  # 1 minute in seconds
attendance_records = []

teacher_rfid = get_teacher_rfid()
t1 = teacher_rfid[len(teacher_rfid)//2:]
t2 = teacher_rfid[:len(teacher_rfid)//2]

try:
    teacher_rfid = wait_for_teacher()
    teacher_id = lookup_teacher_id_by_rfid(teacher_rfid)  # Retrieve teacher_id here
    teacher_class_ids = get_class_for_teacher(teacher_id)  # Retrieve the class IDs for the teacher
    start_time = time.time()  # Initialize start time after teacher is verified
    tdata = teacher_rfid
except Exception as e:
    print("Error during RFID scan:", e)
    exit()

print("Teacher RFID:", teacher_rfid)
print("Teacher ID:", teacher_id)
print("Teacher Class IDs:", teacher_class_ids)

scanned_rfids = set()  # Set to store scanned RFID tags
scanned_qr_codes = set()  # Set to store scanned QR codes
marked_attendance = set()  # Set to store student IDs that have already been marked

while True:
    if arduino.in_waiting > 0:
        rfid_data = arduino.readline().decode('utf-8').rstrip()
        if "Card UID: " in rfid_data:
            rfid_data = rfid_data.replace("Card UID: ", "").strip()
            if rfid_data not in scanned_rfids:
                scanned_rfids.add(rfid_data)
                print("RFID:", rfid_data)

                status = 'present'  # Default status to present

                if rfid_data == t1 or rfid_data == t2:
                    # This is a teacher's RFID
                    teacher_id = lookup_teacher_id_by_rfid(rfid_data)
                    teacher_class_ids = get_class_for_teacher(teacher_id) 
                    print("Teacher detected. Starting attendance capture.")
                    start_time = time.time()
                    if teacher_class_ids: 
                        print(f"Attendance capturing for Class IDs {teacher_class_ids}.")
                    else:
                        print(f"Failed to find classes for Teacher ID {teacher_id}.")
                else:
                    print("Student RFID detected:", rfid_data)
                    studentID = lookup_student_id_by_rfid(rfid_data)
                    student_class_id = lookup_class_id_by_rfid(rfid_data) if studentID else None
                    if studentID and student_class_id in teacher_class_ids:
                        if studentID not in marked_attendance:
                            # Only mark present if student's class ID matches one of the teacher's class IDs and not already marked
                            status = 'present'
                            print(f"Student {lookup_name_by_ID(studentID)} from class {student_class_id} marked as {status}.") 
                            send_attendance(studentID, student_class_id, current_date, current_time, status, teacher_id)
                            marked_attendance.add(studentID)
                            attendance_records.append({
                                'StudentID': studentID,
                                'ClassID': student_class_id,
                                'Date': current_date,
                                'Time': current_time,
                                'Status': status,
                                'TeacherID': teacher_id
                            })
                        else:
                            print(f"Student {studentID} from class {student_class_id} has already been marked present.")
                    else:
                        # Log or handle cases where the student tries to attend the wrong class
                        print(f"Student {studentID} from class {student_class_id} tried to attend class {teacher_class_ids}. Access denied.")
        elif "PICC type: MIFARE" in rfid_data or "MIFARE_Read() failed" in rfid_data:
            # Ignore these messages
            continue
        else:
            print(rfid_data)

        time.sleep(delay_seconds)

    if 'start_time' in locals() and start_time is not None:
        if time.time() - start_time > timeout:
            print("Timeout reached. Stopping attendance capture.")
            break

        ret, frame = cap.read()
        if ret:
            qr_codes = decode(frame)
            for qr_code in qr_codes:
                qr_data = qr_code.data.decode('utf-8')
                if qr_data not in scanned_qr_codes:
                    scanned_qr_codes.add(qr_data)
                    print("QR Code:", qr_data)
                    student_class_id = lookup_class_id_by_qr_code(qr_data)
                    status = 'present'
                    teacher_class_ids = get_class_for_teacher(teacher_id)
                    studentID = lookup_student_id_by_rfid(qr_data)  # Assuming the QR code contains the student ID
                    if studentID not in marked_attendance and student_class_id in teacher_class_ids:
                        current_date = datetime.datetime.now().strftime("%Y-%m-%d")
                        current_time = datetime.datetime.now().strftime("%H:%M:%S")
                        send_attendance(studentID, student_class_id, current_date, current_time, status, teacher_id)
                        marked_attendance.add(studentID)
                        attendance_records.append({
                            'StudentID': studentID,
                            'ClassID': student_class_id,
                            'Date': current_date,
                            'Time': current_time,
                            'Status': status,
                            'TeacherID': teacher_id
                        })
                    else:
                        print(f"Student {lookup_name_by_ID(studentID)} from class {student_class_id} has already been marked present or tried to attend a different class.")
            cv2.imshow('Frame', frame)

            if cv2.waitKey(1) & 0xFF == ord('q'):
                break
        else:
            print("Failed to capture frame")



cap.release()
cv2.destroyAllWindows()
