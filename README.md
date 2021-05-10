# This is a SUT application created for the means of Endava SoA

## Installation using docker(mandatory)

1. Install docker
2. Clone the repo 
3. Ensure sub-derictory DB exists under the project root. If not create an empty directory DB under the project root 
5. Run `docker-compose up -d` This will fetch PHP, MySQL and PYTHON Docker images, launch apache on http://localhost:8080, REST API on http://localhost:5000 and MySQL on port 3306
5.1. Note that during building up Docker will ask for permissions to store data on you file system. Allow all requests.
6. If you want to stop the service just run `docker-compose down`

**Note** 
	If you have troubles running Docker, you'll need to check if Virtualization is enabled in the BIOS settings.
	Video on how to check that [here](https://www.youtube.com/watch?v=1HoIj84zUp0)

## WEB part

 - Note that application comes with pre registered users and one admin. All users have password `pass123`. You may check their specifics from DB.
 - Admin user is  `admin@automation.com`

## REST API end points:

 - Create User
```
   POST /users
   {
      "title" : "<Mr./Mrs.>",
      "first_name" : "<user_firstName>",
      "sir_name" : "<user_surName>",
      "country" : "<user_country>",
      "city" : "<user_city>",
      "email": "<user_email>",
      "password": "<hashed_password>",
      "is_admin": <true/false>
   }
```

 - List Users
```
   GET /users
```

 - List User details
```
   GET /users/<:id>
```

 - Delete User
```
   DELETE /users/<:id>
```

 - Update User
```
   PUT /users/<:id>
   {
      "title" : "<Mr./Mrs.>",
      "first_name" : "<user_firstName>",
      "sir_name" : "<user_surName>",
      "country" : "<user_country>",
      "city" : "<user_city>",
      "email": "<user_email>"
   }
```

 - Login with user (for the www aims)
```
   POST /login
   {
      "email": "<user_email>",
      "password": "<hashed_password>"
   }
```