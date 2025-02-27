from flask import Flask
from flask_cors import CORS
from flask_mysqldb import MySQL
from users import users_bp
from search import search_bp
from skills import skills_bp
import os

def create_app():
    app = Flask(__name__)
    
    # Load MySQL configuration from environment variables
    app.config['MYSQL_USER'] = os.getenv('MYSQL_USER', 'root')
    app.config['MYSQL_PASSWORD'] = os.getenv('MYSQL_PASSWORD', 'pass')
    app.config['MYSQL_DB'] = os.getenv('MYSQL_DB', 'db')
    app.config['MYSQL_HOST'] = os.getenv('MYSQL_HOST', 'mysql')
    
    # Initialize MySQL
    mysql = MySQL(app)
    app.mysql = mysql  # Attach mysql to the app object
    
    # Register blueprints
    app.register_blueprint(users_bp)
    app.register_blueprint(search_bp)
    app.register_blueprint(skills_bp)
    
    return app

app = create_app()
CORS(app)