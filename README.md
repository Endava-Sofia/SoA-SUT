# SoA SUT Application

A web application with user management capabilities, featuring a PHP frontend, Python REST API, and MySQL database.

## System Architecture

The application consists of three main components running in Docker containers:

1. **Frontend (PHP/Apache)**: Serves the web interface on port 8080
2. **REST API (Python/Flask)**: Handles business logic and data operations on port 5000
3. **Database (MySQL)**: Stores application data on port 3306

## Installation Requirements

- Docker Desktop / Rancher Desktop (*use moby Container Engine, https://docs.rancherdesktop.io/getting-started/installation/)
- Git

## Quick Start

1. Clone the repository
2. Navigate to the project directory
3. Run the following command to start all services locally:
```bash
docker compose -f docker-compose-v2.yml up -d
```

## Application Access
- http://localhost:8080
- Default password for all pre-registered users: pass123
- Admin account: admin@automation.com

## Database Access
- Host: localhost
- Port: 3306
- Username: root
- Password: pass
- Connection URL: mysql://localhost:3306

## API Endpoints
- API: http://localhost:5000

### User Management 
#### Create User
``` http
POST /users
Content-Type: application/json

{
    "title": "Mr./Mrs.",
    "first_name": "string",
    "sir_name": "string",
    "country": "string",
    "city": "string",
    "email": "string",
    "password": "<hashed_password>", //password hashing using SHA256
    "is_admin": boolean
}
```
#### Get All Users
``` http
GET /users
```
#### Get User Details
```http
GET /users/{id}
```
#### Update User
```http
PUT /users/{id}
Content-Type: application/json

{
    "title": "Mr./Mrs.",
    "first_name": "string",
    "sir_name": "string",
    "country": "string",
    "city": "string",
    "email": "string"
}
```
#### Delete User
```http
DELETE /users/{id}
```
#### User Authentication
```http
POST /login
Content-Type: application/json

{
    "email": "string",
    "password": "<hashed_password>", //password hashing using SHA256
}
```

### Location Data
#### Get Countries
```http
GET /countries
```
Response:
```json
[
    "Bulgaria",
    "Romania",
    "Greece",
    "Germany",
    "UK",
    "USA"
]
```
#### Get Cities by Country
```http
GET /cities/{country_name}
```
Response:
```json
[
    "Sofia",
    "Sopot",
    "Elin Pelin"
]
```

### Skills Management 
#### Get All Skills
```http
GET /skills
```
Response:
```json
[
    {
        "id": 25,
        "skill_name": "User Acceptance Testing",
        "skill_category": "Quality Assurance",
        "skill_description": "Coordinating and facilitating UAT sessions with stakeholders"
    }
]
```

## Technologies Used
- Frontend: PHP 8.2, Apache, Bootstrap 5
- API: Python 3, Flask
- Database: MySQL 9.0.1
- Container: Docker

## Development
The project uses a microservices architecture with three main services:
- www : PHP frontend service
- rest : Python REST API service
- mysql : Database service

All services are connected through a Docker bridge network named soa-network localy.
And also the project can be deployed in Azure DevOps.
