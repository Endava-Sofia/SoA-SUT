# API Tests Documentation

This directory contains the test suite for the API component of the application. The tests are written using pytest and include both unit and integration tests.

## Test Structure

- `test_profile_picture.py`: Contains tests for profile picture upload functionality
- `conftest.py`: Contains shared pytest fixtures and configuration
- `Dockerfile`: Docker configuration for running tests in a containerized environment

## Test Coverage

The test suite covers the following functionality:

### Profile Picture Tests
- File extension validation
- Image file validation
- File size validation
- Profile picture upload
- Error handling for invalid uploads
- User existence validation

## Running Tests

### Prerequisites
- Docker and Docker Compose installed
- Access to the project root directory

### Running Tests with Docker Compose

The easiest way to run the tests is using Docker Compose:

```bash
# From the project root directory
docker-compose -f docker-compose-v2.yml up test
```

This will:
1. Build the test container
2. Connect to the MySQL database
3. Run all tests with verbose output

### Running Specific Tests

To run specific tests or with different options, you can modify the Dockerfile's ENTRYPOINT or CMD, or run the tests directly:

```bash
# Run a specific test file
docker-compose -f docker-compose-v2.yml run test pytest tests/test_profile_picture.py -v

# Run tests with specific markers
docker-compose -f docker-compose-v2.yml run test pytest -m "marker_name" -v
```

## Test Environment

The test environment includes:
- Python 3.9
- MySQL database connection
- Required Python packages from requirements.txt
- Temporary file storage for test uploads

## Test Database

The tests use a dedicated test database with the following configuration:
- Host: db (Docker service name)
- User: root
- Password: pass
- Database: db

A test user is automatically created and cleaned up for each test session:
- ID: 999
- Email: test@example.com
- Password: password

## Writing New Tests

When adding new tests:
1. Create a new test file in the tests directory
2. Use the existing fixtures from conftest.py when possible
3. Follow the existing test patterns and structure
4. Ensure proper cleanup of test data
5. Add appropriate docstrings and comments

## Best Practices

- Use pytest fixtures for setup and teardown
- Keep tests independent and isolated
- Use meaningful test names and docstrings
- Clean up any created resources
- Follow the project's coding standards 