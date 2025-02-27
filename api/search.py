import MySQLdb
from flask import Blueprint, jsonify, request, current_app

search_bp = Blueprint('search', __name__)

@search_bp.route('/search/users', methods=['POST'])
def search_user():
    print(f"Hello from /search/users")
    data = request.json
    
    if not data:
        return jsonify({"error": "No JSON data provided"}), 400
    
    skills = data.get('skills', [])
    countries = data.get('countries', [])
    cities = data.get('cities', [])
    
    sqlQuery = """select 
                    u.id,
                    u.first_name, 
                    u.sir_name, 
                    u.country, 
                    u.city, 
                    u.email, 
                    s.skill_name, 
                    s.skill_category  
                from users u 
                join user_skills us on us.user_id = u.id 
                JOIN skills s on s.id = us.skill_id
                WHERE 1=1"""
            
    if skills:
        placeholders = ', '.join(['%s' for _ in skills])
        sqlQuery += " AND skill_name in (%s)" % placeholders
    if countries:
        placeholders = ', '.join(['%s' for _ in countries])
        sqlQuery += " AND country IN (%s)" % placeholders
    if cities:
        placeholders = ', '.join(['%s' for _ in cities])
        sqlQuery += " AND city IN (%s)" % placeholders

    try:
        print(f"Quering for {sqlQuery}")
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute(sqlQuery, tuple(skills + countries + cities))
        res = cursor.fetchall()
        print(f"Got result from SQL {sqlQuery} = {res}")
        return jsonify(res)
    except Exception as e:
        print(e)
        return jsonify({"error": str(e)}), 500
        
@search_bp.route('/countries', methods=['GET'])
def search_countries():
    print(f"Hello from /countries")
    
    sqlQuery = """
                select 
                    u.country 
                from users u
                WHERE u.is_admin = 0
                GROUP BY u.country;
                """

    try:
        print(f"Quering for {sqlQuery}")
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute(sqlQuery)
        res = cursor.fetchall()
        print(f"Got result from SQL {sqlQuery} = {res}")
        
        country_names = [entry['country'] for entry in res]
        return jsonify(country_names)
    except Exception as e:
        print(e)
        return jsonify({"error": str(e)}), 500
        
@search_bp.route('/cities/<string:country>', methods=['GET'])
def search_cities(country):
    print(f"Hello from /cities/country")
    
    sqlQuery = f"""
                select 
                    u.city 
                from users u
                WHERE u.is_admin = 0
                AND u.country = '{country}';
                """

    try:
        print(f"Quering for {sqlQuery}")
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        cursor.execute(sqlQuery)
        res = cursor.fetchall()
        print(f"Got result from SQL {sqlQuery} = {res}")
        
        city_names = [entry['city'] for entry in res]
        return jsonify(city_names)
    except Exception as e:
        print(e)
        return jsonify({"error": str(e)}), 500