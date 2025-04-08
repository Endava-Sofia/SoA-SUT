import os
import tempfile
import pytest
from flask import Flask
from werkzeug.datastructures import FileStorage
import sys
sys.path.append('/app')
from users import users_bp, validate_image, allowed_file, UPLOAD_FOLDER, MAX_FILE_SIZE

# Test client fixture
@pytest.fixture
def client(app):
    app.config['UPLOAD_FOLDER'] = tempfile.mkdtemp()
    app.register_blueprint(users_bp)
    return app.test_client()

# Test file fixtures
@pytest.fixture
def valid_image():
    # Create a temporary valid image file
    with tempfile.NamedTemporaryFile(suffix='.jpg', delete=False) as f:
        f.write(b'fake image data')
        return f.name

@pytest.fixture
def large_image():
    # Create a temporary large image file
    with tempfile.NamedTemporaryFile(suffix='.jpg', delete=False) as f:
        f.write(b'0' * (MAX_FILE_SIZE + 1))
        return f.name

@pytest.fixture
def invalid_file():
    # Create a temporary invalid file
    with tempfile.NamedTemporaryFile(suffix='.txt', delete=False) as f:
        f.write(b'not an image')
        return f.name

def test_allowed_file():
    """Test file extension validation"""
    assert allowed_file('test.jpg') is True
    assert allowed_file('test.jpeg') is True
    assert allowed_file('test.png') is True
    assert allowed_file('test.txt') is False
    assert allowed_file('test') is False

def test_validate_image_valid(valid_image):
    """Test validation of valid image file"""
    with open(valid_image, 'rb') as f:
        file = FileStorage(
            stream=f,
            filename='test.jpg',
            content_type='image/jpeg'
        )
        error = validate_image(file)
        assert error is None

def test_validate_image_invalid_type(invalid_file):
    """Test validation of invalid file type"""
    with open(invalid_file, 'rb') as f:
        file = FileStorage(
            stream=f,
            filename='test.txt',
            content_type='text/plain'
        )
        error = validate_image(file)
        assert "Invalid file type" in error

def test_validate_image_too_large(large_image):
    """Test validation of oversized file"""
    with open(large_image, 'rb') as f:
        file = FileStorage(
            stream=f,
            filename='test.jpg',
            content_type='image/jpeg'
        )
        error = validate_image(file)
        assert "File size exceeds" in error

def test_upload_profile_picture(client, valid_image):
    """Test successful profile picture upload"""
    with open(valid_image, 'rb') as f:
        response = client.post(
            '/users/999/profile-picture',
            data={'file': (f, 'test.jpg')},
            content_type='multipart/form-data'
        )
        assert response.status_code == 200
        data = response.get_json()
        assert 'message' in data
        assert data['message'] == 'Profile picture uploaded successfully'
        assert 'profile_picture' in data

def test_upload_profile_picture_invalid_type(client, invalid_file):
    """Test upload with invalid file type"""
    with open(invalid_file, 'rb') as f:
        response = client.post(
            '/users/999/profile-picture',
            data={'file': (f, 'test.txt')},
            content_type='multipart/form-data'
        )
        assert response.status_code == 400
        data = response.get_json()
        assert 'message' in data
        assert 'Invalid file type' in data['message']

def test_upload_profile_picture_too_large(client, large_image):
    """Test upload with oversized file"""
    with open(large_image, 'rb') as f:
        response = client.post(
            '/users/999/profile-picture',
            data={'file': (f, 'test.jpg')},
            content_type='multipart/form-data'
        )
        assert response.status_code == 400
        data = response.get_json()
        assert 'message' in data
        assert 'File size exceeds' in data['message']

def test_upload_profile_picture_no_file(client):
    """Test upload with no file provided"""
    response = client.post(
        '/users/999/profile-picture',
        data={},
        content_type='multipart/form-data'
    )
    assert response.status_code == 400
    data = response.get_json()
    assert 'message' in data
    assert 'No file part' in data['message']

def test_upload_profile_picture_nonexistent_user(client, valid_image):
    """Test upload for non-existent user"""
    with open(valid_image, 'rb') as f:
        response = client.post(
            '/users/999999/profile-picture',
            data={'file': (f, 'test.jpg')},
            content_type='multipart/form-data'
        )
        assert response.status_code == 404
        data = response.get_json()
        assert 'message' in data
        assert 'User not found' in data['message'] 