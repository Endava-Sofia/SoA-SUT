import pymysql
from app import app
from config import mysql
from flask import jsonify
from flask import flash, request

def get_user_by_email(_email):
    sqlQuery = f"SELECT * FROM users WHERE email = '{_email}'"
    try:
        print(f"quering for {sqlQuery}")
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute(sqlQuery)
        res = cursor.fetchone()
        print(f"Got result from SQL {sqlQuery} = {res}")
        return res
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()

def get_user_by_id(_id):
    sqlQuery = f"SELECT * FROM users WHERE id = {_id}"
    try:
        print(f"quering for {sqlQuery}")
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute(sqlQuery)
        res = cursor.fetchone()
        print(f"Got result from SQL {sqlQuery} = {res}")
        return res
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()



@app.route('/users', methods=['POST'])
def add_user():
    has_oppend_con = False
    try:
        _json = request.json
        _firstName = _json['first_name']
        _surName = _json['sir_name']
        _title = _json['title']
        _country = _json['country']
        _city = _json['city']
        _email = _json['email']
        _password = _json['password']
        _isAdmin = _json['is_admin']

        if get_user_by_email(_email) is not None :
            print("User does exist")
            response = jsonify({"message" : "User with such email already exists"})
            response.status_code = 418
            print("returning error")
            return response
        else:
            pass
            print("User does NOT exist")

            _city = _city if _city else ""  #allow the bug from UI to process
        if _firstName and _surName and _title and _country and _email and _password and request.method == 'POST':
            print("saving user")
            sqlQuery = "INSERT INTO users(is_admin, first_name, sir_name, title, country, city, email, password) VALUES(%s, %s, %s, %s, %s, %s, %s, %s)"
            bindData = (_isAdmin, _firstName, _surName, _title, _country, _city, _email, _password)
            conn = mysql.connect()
            cursor = conn.cursor()
            has_oppend_con = True
            cursor.execute(sqlQuery, bindData)
            conn.commit()
            created_user = jsonify(get_user_by_email(_email))
            response = created_user
            response.status_code = 200
            return response
        else:
            return not_found()
    except Exception as e:
        print(e)
    finally:
        if has_oppend_con:
            cursor.close()
            conn.close()

@app.route('/users/<int:id>', methods=['PUT'])
def update_user(id):
    try:
        _json = request.json
        _firstName = _json['first_name']
        _surName = _json['sir_name']
        _title = _json['title']
        _email = _json['email']
        _country = _json['country']
        _city = _json['city']

        esixting_user = get_user_by_email(_email)
        if esixting_user is not None and esixting_user['id'] != id :
            print("User does exist")
            response = jsonify({"message" : "User with such email already exists"})
            response.status_code = 418
            print("returning error")
            return response
        else:
            pass
            print("User does NOT exist")

        # validate the received values
        if _firstName and _surName and _title and _country and _email and _city and request.method == 'PUT':
            sqlQuery = "UPDATE users SET first_name=%s, sir_name=%s, title=%s, email=%s, country=%s, city=%s WHERE id=%s"
            bindData = (_firstName, _surName, _title, _email, _country, _city, id)
            conn = mysql.connect()
            cursor = conn.cursor()
            cursor.execute(sqlQuery, bindData)
            conn.commit()
            response = jsonify(get_user_by_id(id))
            response.status_code = 200
            return response
        else:
            return not_found()
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()



@app.route('/users', methods=['GET'])
def list_users():
    try:
        order_by = request.args.get('order_by')
        orientation = request.args.get('orientation')
        
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        sqlQuery = ""
        if order_by is None:
            print ("retrieving not ordered users")
            sqlQuery = "SELECT * FROM users"
        else:
            orientation = "ASC" if orientation is None else orientation
            sqlQuery = "SELECT * FROM users WHERE is_admin = false ORDER BY %s %s" % (order_by, orientation)
            print ("retrieving users ordered by " + order_by + " and orientation " + orientation)
        
        print("running SQL " + sqlQuery)   
        cursor.execute(sqlQuery)

        usersRows = cursor.fetchall()
        respone = jsonify(usersRows)
        respone.status_code = 200
        return respone
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()


@app.route('/users/<int:id>', methods=['GET'])
def get_user(id):
    try:
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT * FROM users WHERE id =%s limit 1", id)
        empRow = cursor.fetchone()
        response = jsonify(empRow)
        response.status_code = 200
        return response
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()

@app.route('/login', methods=['POST'])
def login():
    try:
        print("Enter login method")
        _json = request.json
        
        _email = _json['email']
        _password = _json['password']
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT * FROM users WHERE email = %s AND password = %s limit 1", [_email, _password])
        print("My SQL queried with " + _email + " and " + _password)
        
        if cursor.rowcount < 1 :
            response = jsonify({"message" : "Invalid user or email"})
            response.status_code = 401
            return response
       
        empRow = cursor.fetchone()
        response = jsonify(empRow)
        response.status_code = 200
        return response
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()


@app.route('/users/<int:id>', methods=['DELETE'])
def delete_user(id):
    try:
        conn = mysql.connect()
        cursor = conn.cursor()
        cursor.execute("DELETE FROM users WHERE id =%s", id)
        conn.commit()
        response = jsonify('User deleted successfully!')
        response.status_code = 200
        return response
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()


@app.errorhandler(404)
def not_found(error=None):
    message = {
        'status': 404,
        'message': 'Record not found: ' + request.url,
    }
    response = jsonify(message)
    response.status_code = 404
    return response


if __name__ == "__main__":
    print("Starting application")
    app.run(host='0.0.0.0')
    print("application is started")
    
