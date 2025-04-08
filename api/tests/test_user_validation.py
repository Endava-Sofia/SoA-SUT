import pytest
import sys
import time
sys.path.append('/app')
from flask import jsonify

@pytest.fixture
def client(app):
    """Create a test client"""
    from users import users_bp
    from search import search_bp
    
    # Register blueprints
    app.register_blueprint(users_bp)
    app.register_blueprint(search_bp)
    
    with app.app_context():
        # Ensure cities table exists with test data
        conn = app.mysql.connection
        cursor = conn.cursor()
        
        # Create cities table if needed
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS cities (
                id INT(11) NOT NULL AUTO_INCREMENT,
                city_name VARCHAR(100) NOT NULL,
                country_name VARCHAR(100) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY unique_city_country (city_name, country_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        """)
        
        # Add test cities
        test_cities = [
            ('Sofia', 'Bulgaria'),
            ('Sopot', 'Bulgaria'),
            ('Elin Pelin', 'Bulgaria'),
            ('London', 'UK'),
            ('Berlin', 'Germany'),
            ('New York', 'USA')
        ]
        for city, country in test_cities:
            cursor.execute(
                "INSERT IGNORE INTO cities (city_name, country_name) VALUES (%s, %s)",
                (city, country)
            )
        conn.commit()
        
    return app.test_client()

def test_create_user_with_invalid_city_country_combination(client):
    """Test that user creation fails when city doesn't belong to the specified country"""
    # Test data with invalid city-country combination
    test_data = {
        "first_name": "John",
        "sir_name": "Doe",
        "title": "Mr.",
        "country": "Bulgaria",
        "city": "London",  # London is not in Bulgaria
        "email": f"test_invalid_{int(time.time())}@example.com",  # Use timestamp for unique email
        "password": "password123"
    }

    # Make request to create user
    response = client.post('/users', json=test_data)
    
    # Assert that the request fails with appropriate error
    assert response.status_code == 400
    assert "City does not belong to the specified country" in response.get_json()["message"]

def test_create_user_with_valid_city_country_combination(client):
    """Test that user creation succeeds with valid city-country combination"""
    # Test data with valid city-country combination
    test_data = {
        "first_name": "John",
        "sir_name": "Doe",
        "title": "Mr.",
        "country": "Bulgaria",
        "city": "Sofia",  # Sofia is in Bulgaria
        "email": f"test_valid_{int(time.time())}@example.com",  # Use timestamp for unique email
        "password": "password123"
    }

    # Make request to create user
    response = client.post('/users', json=test_data)
    
    # Assert that the request succeeds
    assert response.status_code == 200
    user_data = response.get_json()
    assert user_data["country"] == "Bulgaria"
    assert user_data["city"] == "Sofia"

def test_get_cities_by_country(client):
    """Test that cities endpoint returns correct cities for a country"""
    # Test getting cities for Bulgaria
    response = client.get('/cities/Bulgaria')
    
    # Assert that the request succeeds
    assert response.status_code == 200
    cities = response.get_json()
    
    # Assert that Sofia is in the list of Bulgarian cities
    assert "Sofia" in cities
    # Assert that London is not in the list of Bulgarian cities
    assert "London" not in cities

def test_search_users_by_city_country(client):
    """Test that search returns correct results based on city-country validation"""
    # First create a user with valid city-country combination
    unique_email = f"test_search_{int(time.time())}@example.com"
    test_data = {
        "first_name": "John",
        "sir_name": "Doe",
        "title": "Mr.",
        "country": "Bulgaria",
        "city": "Sofia",
        "email": unique_email,
        "password": "password123"
    }
    
    # Create the user
    client.post('/users', json=test_data)
    
    # Search for users in Bulgaria with Sofia as city (using /search/users endpoint)
    search_data = {
        "countries": ["Bulgaria"],
        "cities": ["Sofia"]
    }
    response = client.post('/search/users', json=search_data)
    assert response.status_code == 200
    results = response.get_json()
    assert len(results) > 0
    
    # Search for users in Bulgaria with London as city (should return no results)
    search_data = {
        "countries": ["Bulgaria"],
        "cities": ["London"]
    }
    response = client.post('/search/users', json=search_data)
    assert response.status_code == 200
    results = response.get_json()
    assert len(results) == 0 