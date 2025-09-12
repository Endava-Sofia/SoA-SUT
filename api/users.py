import MySQLdb
import re
import os
from flask import Blueprint, jsonify, request, current_app, send_from_directory, url_for
from werkzeug.utils import secure_filename

users_bp = Blueprint('users', __name__)

# Configure upload folder
UPLOAD_FOLDER = '/app/uploads/profile_pictures'
ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg'}
MAX_FILE_SIZE = 5 * 1024 * 1024  # 5MB

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

def validate_image(file):
    """Validate uploaded image file"""
    if not file:
        return "No file provided"
    if not allowed_file(file.filename):
        return "Invalid file type. Allowed types: png, jpg, jpeg"
    if len(file.read()) > MAX_FILE_SIZE:
        file.seek(0)  # Reset file pointer after reading
        return "File size exceeds 5MB limit"
    file.seek(0)  # Reset file pointer after reading
    return None

def validate_city_country(city, country):
    """Validate if city belongs to the specified country"""
    try:
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute("SELECT COUNT(*) as count FROM cities WHERE city_name = %s AND country_name = %s", [city, country])
        result = cursor.fetchone()
        count = result['count'] if result else 0
        print(f"Validation result for {city}, {country}: count={count}")
        return count > 0
    except Exception as e:
        print(f"Error validating city-country: {str(e)}")
        return False

def validate_input(data):
    """Validate user input according to requirements"""
    errors = []
    
    # Check required fields
    required_fields = ['first_name', 'sir_name', 'title', 'country', 'email']
    for field in required_fields:
        if field not in data or not data[field]:
            errors.append(f"{field.replace('_', ' ').title()} is required")
    
    # Validate field lengths (2-15 characters)
    for field in ['first_name', 'sir_name', 'title', 'country', 'city']:
        if field in data and data[field]:
            if not 2 <= len(data[field]) <= 15:
                errors.append(f"{field.replace('_', ' ').title()} must be between 2 and 15 characters")
    
    # Validate name fields (no special characters)
    if 'first_name' in data and not re.match(r'^[a-zA-Z\s]+$', data['first_name']):
        errors.append("First name cannot contain special characters")
    if 'sir_name' in data and not re.match(r'^[a-zA-Z\s]+$', data['sir_name']):
        errors.append("Sir name cannot contain special characters")
    
    # Validate email format
    if 'email' in data and not re.match(r'^[\w\.-]+@[\w\.-]+\.\w+$', data['email']):
        errors.append("Invalid email format")
    
    # Validate city-country combination
    if 'city' in data and 'country' in data and data['city'] and data['country']:
        if not validate_city_country(data['city'], data['country']):
            errors.append("City does not belong to the specified country")
    
    return errors

def get_user_by_email(_email):
    try:
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute("SELECT * FROM users WHERE email = %s", [_email])
        user = cursor.fetchone()
        return user
    except Exception as e:
        print(f"Error getting user by email: {str(e)}")
        return None

def get_user_by_id(_id):
    try:
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute("SELECT * FROM users WHERE id = %s", [_id])
        user = cursor.fetchone()
        return user
    except Exception as e:
        print(f"Error getting user by id: {str(e)}")
        return None

@users_bp.route('/users', methods=['POST'])
def add_user():
    try:
        _json = request.json
        if not _json:
            return jsonify({"message": "No input data provided"}), 400

        errors = validate_input(_json)
        if errors:
            return jsonify({"message": errors[0] if len(errors) == 1 else errors}), 400

        if get_user_by_email(_json['email']):
            return jsonify({"message": "User with such email already exists"}), 409

        sqlQuery = """INSERT INTO users(is_admin, first_name, sir_name, title, country, 
                   city, email, password) VALUES(%s, %s, %s, %s, %s, %s, %s, %s)"""
        bindData = (_json.get('is_admin', False), _json['first_name'], _json['sir_name'],
                   _json['title'], _json['country'], _json.get('city', ''),
                   _json['email'], _json['password'])
        
        conn = current_app.mysql.connection
        cursor = conn.cursor()
        cursor.execute(sqlQuery, bindData)
        conn.commit()
        
        return jsonify(get_user_by_email(_json['email'])), 200
    except KeyError as e:
        return jsonify({"message": f"Missing required field: {str(e)}"}), 400
    except Exception as e:
        print(f"Error adding user: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.route('/login', methods=['POST'])
def login():
    try:
        _json = request.json
        if not _json.get('email') or not _json.get('password'):
            return jsonify({"message": "Email and password are required"}), 400

        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute("SELECT * FROM users WHERE email = %s AND password = %s", 
                      [_json['email'], _json['password']])
        
        user = cursor.fetchone()
        if not user:
            return jsonify({"message": "Invalid email or password"}), 401
        
        return jsonify(user), 200
    except Exception as e:
        print(f"Error during login: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.route('/users/<int:id>/profile-picture', methods=['POST'])
def upload_profile_picture(id):
    try:
        # Check if user exists
        user = get_user_by_id(id)
        if not user:
            return jsonify({"message": "User not found"}), 404

        if 'file' not in request.files:
            return jsonify({"message": "No file part"}), 400
        
        file = request.files['file']
        if file.filename == '':
            return jsonify({"message": "No selected file"}), 400

        # Validate image
        error = validate_image(file)
        if error:
            return jsonify({"message": error}), 400

        # Create secure filename with user ID
        filename = f"{id}_{secure_filename(file.filename)}"
        
        # Ensure upload directory exists
        os.makedirs(UPLOAD_FOLDER, exist_ok=True)
        
        # Save file
        file_path = os.path.join(UPLOAD_FOLDER, filename)
        file.save(file_path)
        
        # Update user's profile picture in database
        full_path = f"uploads/profile_pictures/{filename}" # Construct the full path
        conn = current_app.mysql.connection
        cursor = conn.cursor()
        # Store the full path in the database
        cursor.execute("UPDATE users SET profile_picture = %s WHERE id = %s", (full_path, id)) 
        conn.commit()
        
        return jsonify({
            "message": "Profile picture uploaded successfully",
            # Return the full path in the response
            "profile_picture": full_path 
        }), 200

    except Exception as e:
        print(f"Error uploading profile picture: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.route('/users/<int:id>', methods=['PUT'])
def update_user(id):
    try:
        _json = request.json
        errors = validate_input(_json)
        if errors:
            return jsonify({"errors": errors}), 400

        existing_user = get_user_by_email(_json['email'])
        if existing_user and existing_user['id'] != id:
            return jsonify({"message": "Email already in use"}), 409

        sqlQuery = """UPDATE users SET first_name=%s, sir_name=%s, title=%s, 
                     email=%s, country=%s, city=%s, profile_picture=%s WHERE id=%s"""
        bindData = (_json['first_name'], _json['sir_name'], _json['title'],
                   _json['email'], _json['country'], _json.get('city', ''),
                   _json.get('profile_picture'), id)
        
        conn = current_app.mysql.connection
        cursor = conn.cursor()
        cursor.execute(sqlQuery, bindData)
        conn.commit()
        
        return jsonify(get_user_by_id(id)), 200
    except Exception as e:
        print(f"Error updating user: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.route('/users', methods=['GET'])
def list_users():
    try:
        order_by = request.args.get('orderBy')
        sort = request.args.get('sort', 'asc')
        
        # Validate order_by parameter to prevent SQL injection
        valid_columns = ['first_name', 'sir_name', 'email', 'country', 'city', 'title']
        if order_by and order_by not in valid_columns:
            return jsonify({"message": "Invalid sort column"}), 400
        
        # Validate sort parameter
        if sort.lower() not in ['asc', 'desc']:
            return jsonify({"message": "Invalid sort direction"}), 400
        
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        
        if order_by:
            sqlQuery = f"SELECT * FROM users ORDER BY {order_by} {sort}"
            cursor.execute(sqlQuery)
        else:
            cursor.execute("SELECT * FROM users")
        
        users = cursor.fetchall()
        return jsonify(users), 200
    except Exception as e:
        print(f"Error listing users: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.route('/users/<int:id>', methods=['GET'])
def get_user(id):
    try:
        user = get_user_by_id(id)
        if not user:
            return jsonify({"message": "User not found"}), 404
        return jsonify(user), 200
    except Exception as e:
        print(f"Error getting user: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.route('/users/<int:id>', methods=['DELETE'])
def delete_user(id):
    try:
        user = get_user_by_id(id)
        if not user:
            return jsonify({"message": "User not found"}), 404
            
        conn = current_app.mysql.connection
        cursor = conn.cursor()
        cursor.execute("DELETE FROM users WHERE id = %s", [id])
        conn.commit()
        return jsonify({"message": "User deleted successfully"}), 200
    except Exception as e:
        print(f"Error deleting user: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.route('/cities/<country_name>', methods=['GET'])
def get_cities_by_country(country_name):
    try:
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute("SELECT city_name FROM cities WHERE country_name = %s", [country_name])
        cities = cursor.fetchall()
        return jsonify([city['city_name'] for city in cities]), 200
    except Exception as e:
        print(f"Error getting cities: {str(e)}")
        return jsonify({"message": "Internal server error"}), 500

@users_bp.errorhandler(404)
def not_found(error=None):
    return jsonify({
        'status': 404,
        'message': 'Resource not found: ' + request.url
    }), 404