import MySQLdb
from flask import Blueprint, jsonify, request, current_app

skills_bp = Blueprint('skills', __name__)

@skills_bp.route('/skills', methods=['GET'])
def list_skills():
    try:
        conn = current_app.mysql.connection
        cursor = conn.cursor(MySQLdb.cursors.DictCursor)
        order_by = "skill_category"
        orientation = "ASC"
        sqlQuery = "SELECT * FROM skills ORDER BY %s %s" % (order_by, orientation)
        print ("Retrieving skills ordered by " + order_by + " and orientation " + orientation)
        
        print("Running SQL " + sqlQuery)   
        cursor.execute(sqlQuery)

        skillsRows = cursor.fetchall()
        respone = jsonify(skillsRows)
        respone.status_code = 200
        return respone
    except Exception as e:
        print(e)