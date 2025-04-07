import MySQLdb
import re
from flask import Blueprint, jsonify, request, current_app

users_bp = Blueprint('users', __name__)

def validate_input(data):
    """Validate user input according to requirements"""
    errors = []
    
    # Check required fields
    required_fields = ['first_name', 'sir_name', 'title', 'country', 'email', 'password']
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
    
    return errors

def get_user_by_email(_email):
    try:
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute("SELECT * FROM users WHERE email = %s", [_email])
        return cursor.fetchone()
    except Exception as e:
        print(f"Error getting user by email: {str(e)}")
        return None

def get_user_by_id(_id):
    try:
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute("SELECT * FROM users WHERE id = %s", [_id])
        return cursor.fetchone()
    except Exception as e:
        print(f"Error getting user by id: {str(e)}")
        return None

@users_bp.route('/users', methods=['POST'])
def add_user():
    try:
        _json = request.json
        errors = validate_input(_json)
        if errors:
            return jsonify({"errors": errors}), 400

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
                     email=%s, country=%s, city=%s WHERE id=%s"""
        bindData = (_json['first_name'], _json['sir_name'], _json['title'],
                   _json['email'], _json['country'], _json.get('city', ''), id)
        
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
        order_by = request.args.get('order_by')
        orientation = request.args.get('orientation', 'ASC')
        
        # Validate order_by parameter to prevent SQL injection
        valid_columns = ['first_name', 'sir_name', 'email', 'country', 'city']
        if order_by and order_by not in valid_columns:
            return jsonify({"message": "Invalid sort column"}), 400
        
        # Validate orientation parameter
        if orientation.upper() not in ['ASC', 'DESC']:
            return jsonify({"message": "Invalid sort direction"}), 400
        
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        
        if order_by:
            sqlQuery = "SELECT * FROM users WHERE is_admin = false ORDER BY %s %s"
            cursor.execute(sqlQuery, (order_by, orientation))
        else:
            cursor.execute("SELECT * FROM users WHERE is_admin = false")
        
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

@users_bp.errorhandler(404)
def not_found(error=None):
    return jsonify({
        'status': 404,
        'message': 'Resource not found: ' + request.url
    }), 404