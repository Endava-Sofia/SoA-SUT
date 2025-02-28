# SoA SUT Application

A web application with user management capabilities, featuring a PHP frontend, Python REST API, and MySQL database.

## System Architecture

The application consists of three main components running in Docker containers:

1. **Frontend (PHP/Apache)**: Serves the web interface on port 8080
2. **REST API (Python/Flask)**: Handles business logic and data operations on port 5000
3. **Database (MySQL)**: Stores application data on port 3306

## Installation Requirements

- Docker Desktop / Rancher Desktop
- Git

## Quick Start

1. Clone the repository
2. Navigate to the project directory
3. Run the following command to start all services locally:
```bash
docker-compose -f docker-compose-v2.yml up
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
    "password": "string",
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
    "password": "string"
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

## Trae AI
Used 24 References

I'll analyze the project structure and help update the README.md file with more accurate and detailed information. Based on the code snippets provided, here's an improved version of the README.md:

```markdown
# SoA SUT Application

A web application with user management capabilities, featuring a PHP frontend, Python REST API, and MySQL database.

## System Architecture

The application consists of three main components running in Docker containers:

1. **Frontend (PHP/Apache)**: Serves the web interface on port 8080
2. **REST API (Python/Flask)**: Handles business logic and data operations on port 5000
3. **Database (MySQL)**: Stores application data on port 3306

## Installation Requirements

- Docker Desktop
- Git

## Quick Start

1. Clone the repository
2. Navigate to the project directory
3. Create an empty `DB` directory in the project root (if not exists)
4. Run the following command to start all services:
```bash
docker-compose up -d
 ```
```

5. Access the application at http://localhost:8080
6. To stop all services:
```bash
docker-compose down
 ```

Note : Ensure hardware virtualization is enabled in your BIOS settings if you experience Docker-related issues.

## Database Access
- Host: localhost
- Port: 3306
- Username: root
- Password: pass
- Connection URL: mysql://localhost:3306
## User Authentication
- Default password for all pre-registered users: pass123
- Admin account: admin@automation.com
## API Endpoints
### User Management Create User
```http
POST /users
Content-Type: application/json

{
    "title": "Mr./Mrs.",
    "first_name": "string",
    "sir_name": "string",
    "country": "string",
    "city": "string",
    "email": "string",
    "password": "string",
    "is_admin": boolean
}
 ```
 Get All Users
```http
GET /users
 ```
 Get User Details
```http
GET /users/{id}
 ```
 Update User
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
 Delete User
```http
DELETE /users/{id}
 ```
 User Authentication
```http
POST /login
Content-Type: application/json

{
    "email": "string",
    "password": "string"
}
 ```

### Location Data Get Countries
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
 Get Cities by Country
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

### Skills Management Get All Skills
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
```

### Search Functionality Search Users
```http
POST /search/users
Content-Type: application/json

{
    "skills": ["skill1", "skill2"],
    "countries": ["country1", "country2"],
    "cities": ["city1", "city2"]
}
 ```

Response:

```json
[
    {
        "id": 2,
        "first_name": "Ivan",
        "sir_name": "Dimotrov",
        "email": "idimitrov@automation.com",
        "country": "Bulgaria",
        "city": "Sofia",
        "skill_name": "Java",
        "skill_category": "Programming"
    }
]
 ```
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
All services are connected through a Docker bridge network named soa-network .

```plaintext

This updated README.md provides:
1. Better structure and organization
2. More detailed API documentation
3. Clear installation instructions
4. Technology stack information
5. Development architecture details
6. Improved formatting and readability

The main improvements include:
- Added system architecture overview
- Clarified technology versions
- Better organized API documentation
- Added development section
- Improved formatting for better readability
- Added more detailed response examples
 ```
```

## Georgi Kokonov
Copy

Delete

Make the changes to the readme.md file

## Trae AI
I'll help you update the README.md file. I'll provide the complete updated content that you can directly save to the file.

```markdown
# SoA SUT Application

A web application with user management capabilities, featuring a PHP frontend, Python REST API, and MySQL database.

## System Architecture

The application consists of three main components running in Docker containers:

1. **Frontend (PHP/Apache)**: Serves the web interface on port 8080
2. **REST API (Python/Flask)**: Handles business logic and data operations on port 5000
3. **Database (MySQL)**: Stores application data on port 3306

## Installation Requirements

- Docker Desktop
- Git

## Quick Start

1. Clone the repository
2. Navigate to the project directory
3. Create an empty `DB` directory in the project root (if not exists)
4. Run the following command to start all services:
```bash
docker-compose up -d
 ```
```

5. Access the application at http://localhost:8080
6. To stop all services:
```bash
docker-compose down
 ```

Note : Ensure hardware virtualization is enabled in your BIOS settings if you experience Docker-related issues.

## Database Access
- Host: localhost
- Port: 3306
- Username: root
- Password: pass
- Connection URL: mysql://localhost:3306
## User Authentication
- Default password for all pre-registered users: pass123
- Admin account: admin@automation.com
## API Endpoints
### User Management Create User
```http
POST /users
Content-Type: application/json

{
    "title": "Mr./Mrs.",
    "first_name": "string",
    "sir_name": "string",
    "country": "string",
    "city": "string",
    "email": "string",
    "password": "string",
    "is_admin": boolean
}
 ```
 Get All Users
```http
GET /users
 ```
 Get User Details
```http
GET /users/{id}
 ```
 Update User
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
 Delete User
```http
DELETE /users/{id}
 ```
 User Authentication
```http
POST /login
Content-Type: application/json

{
    "email": "string",
    "password": "string"
}
 ```

### Location Data Get Countries
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
 Get Cities by Country
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

### Skills Management Get All Skills
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

### Search Functionality 
#### Search Users
``` http
POST /search/users
Content-Type: application/json

{
    "skills": ["skill1", "skill2"],
    "countries": ["country1", "country2"],
    "cities": ["city1", "city2"]
}
```
Response:
```json
[
    {
        "id": 2,
        "first_name": "Ivan",
        "sir_name": "Dimotrov",
        "email": "idimitrov@automation.com",
        "country": "Bulgaria",
        "city": "Sofia",
        "skill_name": "Java",
        "skill_category": "Programming"
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

All services are connected through a Docker bridge network named soa-network localy. And also can be deployed in Azure DevOps.