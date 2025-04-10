# QA Hackathon Solution

## AI Tools Used
- Cursor AI (Pro plan has 2 weeks trial) 
- Wispr Flow (Free)

## AI Prompting Approach
The success of using AI tools in development heavily depends on how effectively we communicate with them. Here's a detailed breakdown of my prompting strategy:

1. **Context Management**
   - Configured comprehensive Cursor Rules for the project, serving as guidelines and best practices
   - Rules cover PHP frontend, Python REST API, MySQL database, and Docker implementation
   - Rules are automatically added as context to each AI interaction
   - Used https://cursor.directory/ as a reference for creating these guidelines

2. **Prompt Structure**
   - Break down complex tasks into smaller, manageable prompts
   - Start with clear, specific questions or tasks
   - Provide relevant context (files, folders, code snippets)
   - Include any necessary role specifications (e.g., "Act as a senior PHP developer...")
   - Use specific technical terminology (@Docs adds relevant documentation, and you can reference external sites as documentation as well)
   - Include error messages or specific issues when troubleshooting (Adding screenshots also helps)

## Challenges Encountered
The main challenge I encountered was ensuring I provided the correct and complete context in my prompts. The AI agent had a tendency to:
- Duplicate existing functionality
- Create additional, unrequested features

These challenges could be mitigated by:
- Providing better code descriptions
- Fine-tuning project guidelines
- Being more explicit about what should and shouldn't be modified

## Overcoming AI Limitations
I encountered some limitations with Cursor AI, particularly in its inability to:
- Directly interact with Docker containers
- Access web browsers for debugging purposes

To work around these limitations, I had to:
- Manually add screenshots and logs to provide context
- Share specific error states and visual issues

For example, when troubleshooting a profile picture display issue, the agent couldn't effectively diagnose the problem until I manually attached several screenshots showing the exact behavior.

While these limitations exist, the manual addition of context proved to be a practical solution rather than spending time trying to overcome the limitations through other means.

## Project Guidelines Reference
Below are the comprehensive guidelines used throughout the project:

### General Principles

1. **Separation of Concerns**
   - Ensure each component (frontend, API, database) handles its specific responsibilities
   - Avoid mixing presentation logic in the API or business logic in the frontend

2. **Security First**
   - Implement robust input validation and sanitization at all layers
   - Use parameterized queries or prepared statements to prevent SQL injection
   - Enforce HTTPS for all communication
   - Store sensitive data securely (e.g., hashed and salted, environment variables)
   - Implement user authentication and authorization

3. **Maintainability**
   - Write clean, well-documented code
   - Follow consistent coding standards (PSR for PHP, PEP 8 for Python)
   - Use meaningful variable and function names
   - Break down complex logic into smaller, reusable functions
   - Use version control

4. **Performance**
   - Optimize database queries for efficiency
   - Implement caching where appropriate
   - Minimize network requests between components
   - Use efficient data structures and algorithms

5. **Error Handling**
   - Implement robust error handling and logging
   - Return informative error messages to the client
   - Handle exceptions gracefully
   - Do not display sensitive error information to end users

6. **Dockerization**
   - Use Docker Compose to manage the application's services
   - Define clear Dockerfiles for each component
   - Use environment variables for configuration
   - Implement volume mounts for data persistence
   - Use docker networks to isolate the containers

### PHP Frontend Specific Rules

7. **Templating**
   - Use a templating engine to separate presentation logic from PHP code
   - Avoid embedding large amounts of PHP within HTML

8. **Input Validation**
   - Validate all user input on both client-side and server-side
   - Sanitize input to prevent cross-site scripting (XSS) attacks

9. **API Communication**
   - Use appropriate HTTP client libraries to interact with the Python REST API
   - Handle API responses gracefully, including error codes
   - Use appropriate HTTP methods (GET, POST, PUT, DELETE)

10. **Session Management**
    - Use PHP's session management capabilities
    - Secure session cookies

11. **PSR Standards**
    - Follow PSR coding standards

### Python REST API Specific Rules

12. **Framework Usage**
    - Utilize Flask's features for routing, request handling, and response generation
    - Use Flask extensions for common tasks

13. **Data Serialization**
    - Use JSON for data exchange between the API and frontend
    - Implement proper data serialization and deserialization

14. **Database Interaction**
    - Use an ORM like SQLAlchemy to interact with the MySQL database
    - Avoid writing raw SQL queries directly
    - Use try-except blocks to handle database errors

15. **API Design**
    - Follow RESTful API design principles
    - Use appropriate HTTP status codes
    - Implement versioning for your API

16. **Logging**
    - Implement robust logging
    - Log all errors and important events

17. **PEP 8 Standards**
    - Follow PEP 8 coding standards

### MySQL Database Specific Rules

18. **Schema Design**
    - Design a normalized database schema to minimize data redundancy
    - Use appropriate data types and constraints
    - Index frequently queried columns

19. **Security**
    - Restrict database access to the API container
    - Use strong passwords for database users
    - Minimize database user privileges

20. **Performance**
    - Optimize database queries using indexes and query profiling
    - Tune MySQL configuration parameters for optimal performance
    - Regularly backup the database

21. **Data Integrity**
    - Enforce data integrity using database constraints
    - Use transactions to ensure data consistency

### Docker Specific Rules

22. **Docker Compose**
    - Use Docker compose to define and run multi-container applications
    - Define the networks within the docker compose file

23. **Docker files**
    - Use multi-stage docker files when appropriate
    - Minimize the size of the docker images
    - Do not store sensitive data in the docker files

24. **Docker Volumes**
    - Use docker volumes for data persistence
    - Do not store sensitive data in volumes that are not encrypted 