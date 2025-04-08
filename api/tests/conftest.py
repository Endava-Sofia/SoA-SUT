import os
import pytest
from flask import Flask
from flask_mysqldb import MySQL
import time

@pytest.fixture(scope='session')
def app():
    """Create a Flask application for the tests"""
    app = Flask(__name__)
    app.config['TESTING'] = True
    app.config['MYSQL_HOST'] = os.getenv('MYSQL_HOST', 'db')
    app.config['MYSQL_USER'] = os.getenv('MYSQL_USER', 'root')
    app.config['MYSQL_PASSWORD'] = os.getenv('MYSQL_PASSWORD', 'pass')
    app.config['MYSQL_DB'] = os.getenv('MYSQL_DB', 'db')
    app.config['MYSQL_CURSORCLASS'] = 'DictCursor'
    
    # Import and register blueprints
    import sys
    sys.path.append('/app')
    from users import users_bp
    app.register_blueprint(users_bp)
    
    return app

@pytest.fixture(scope='session')
def test_db(app):
    """Create a test database connection"""
    mysql = MySQL(app)
    app.mysql = mysql  # Attach mysql to the app object
    
    # Wait for MySQL to be ready
    max_retries = 5
    retry_delay = 5
    for i in range(max_retries):
        try:
            with app.app_context():
                conn = mysql.connection
                if conn:
                    return mysql
        except Exception as e:
            if i == max_retries - 1:
                raise e
            time.sleep(retry_delay)
    
    return mysql

@pytest.fixture(autouse=True)
def setup_test_db(app, test_db):
    """Setup test database before each test"""
    with app.app_context():
        try:
            conn = test_db.connection
            if not conn:
                # Try to reconnect
                conn = test_db.connect()
            
            cursor = conn.cursor()
            
            # Ensure cities table exists
            cursor.execute("""
                CREATE TABLE IF NOT EXISTS cities (
                  id int(11) NOT NULL AUTO_INCREMENT,
                  city_name varchar(100) NOT NULL,
                  country_name varchar(100) NOT NULL,
                  PRIMARY KEY (id),
                  UNIQUE KEY unique_city_country (city_name, country_name)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1
            """)
            
            # Check if cities table has data, if not add test cities
            cursor.execute("SELECT COUNT(*) FROM cities")
            city_count = cursor.fetchone()['COUNT(*)']
            if city_count == 0:
                test_cities = [
                    ('Sofia', 'Bulgaria'),
                    ('Sopot', 'Bulgaria'),
                    ('Elin Pelin', 'Bulgaria'),
                    ('London', 'UK'),
                    ('Berlin', 'Germany'),
                    ('New York', 'USA')
                ]
                cursor.executemany(
                    "INSERT IGNORE INTO cities (city_name, country_name) VALUES (%s, %s)",
                    test_cities
                )
                conn.commit()
            
            # Check if test user exists, if not create it
            cursor.execute("SELECT id FROM users WHERE email = 'test@example.com'")
            if not cursor.fetchone():
                cursor.execute("""
                    INSERT INTO users (id, first_name, sir_name, title, country, city, email, password)
                    VALUES (999, 'Test', 'User', 'Mr.', 'Bulgaria', 'Sofia', 'test@example.com', 'password')
                """)
                conn.commit()
            
            yield
            
            # Cleanup: remove test user if it exists
            cursor.execute("DELETE FROM users WHERE email = 'test@example.com'")
            conn.commit()
            cursor.close()
        except Exception as e:
            pytest.fail(f"Database setup failed: {str(e)}") 