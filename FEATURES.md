# SoA SUT Web Application Requirements Document

## Features Overview
This document outlines the key features of the Web Application.

## Registration
### Description
The user must register before being able to access and use the application.

### Precondition
- The user has not previously registered.

### Narrative
As a new user of SoA SUT,  
In order to securely access my account,  
I want to register.

### Main Scenario
- **Given** I open the application for the first time,  
- **When** I enter all of the required information,  
- **Then** my registration is successful,  
- **And** I am redirected to my home page.

### Acceptance Criteria
1. All input fields are required for successful registration.
2. The email field must be **unique**, ensuring that only one user can be registered with a given email address.
3. The email field must contain a **valid** email format.
4. All input fields must have a **minimum of 2 characters** and a **maximum of 15 characters**.
5. The user must agree to the **Terms of Service** to complete registration.
6. **First Name** and **Sir Name** must not accept special symbols.
7. If any required information is missing, an **error message** should be displayed.

## Login
### Description
Registered users should be able to log in to the System under Test (SoA SUT).

### Precondition
- The user must have an existing account with valid credentials.

### Narrative
As a registered user of SoA SUT,  
In order to use the site,  
I want to log in.  

### Acceptance Criteria
- The user can enter their email/username and password to authenticate.
- If the login credentials are valid, the user is redirected to the home page.
- If the password is entered incorrectly three times, the user account is locked.
- Appropriate error messages are displayed for incorrect login attempts.

## Update Profile
### Description
Registered users should be able to update their profile details to keep them up to date.

### Precondition
- The user must be logged in to access the profile update page.

### Narrative
As a registered user of SoA SUT,  
In order to keep my profile information accurate,  
I want to update my profile properties.

### Acceptance Criteria
- Only registered users can update their profile.
- The user can access the profile page from the home page by clicking on their email in the top-right corner.
- When the user lands on their profile page:
  - The page header should be **“Update Profile”**.
  - The profile form should be populated with the user’s existing data.
- The user should be able to update the following mandatory fields:
  - **Title***
  - **First Name***
  - **Sir Name***
  - **Password***
  - **Country***
  - **City***
- If any field is left empty upon submission:
  - A warning popup should display.
  - The profile page should remain open.
- If the update is successful:
  - The page header should change to **“Profile Updated”**.
  - The updated profile information should be reflected throughout the application, including the Home Page welcome message.

## List Users

### Description
Registered users should be able to view a list of existing users in the system.

### Precondition
- The user must be logged in to access the user list.

### Narrative
As a registered user of SoA SUT,  
In order to view details of other users,  
I want to list all existing users.

### Acceptance Criteria
- Only logged-in users can access the user list.
- The list displays the following user details:
  - **Title**
  - **First Name**
  - **Sir Name**
  - **Email**
  - **Country**
  - **City**
- The user list is presented in a **table format**.
- The table includes **pagination**, displaying **10 records per page**.
- **Sorting functionality** is available for all columns.
- **Admin-specific functionality:**
  - Only Admin users can view other Admin users.
  - Only Admin users can delete users from the system.
  - **Only Admin users can see an "Add User" button** that allows them to register new users.
  - Clicking the **"Add User" button** opens a **user registration form**.
  - The form contains the same **validations** as the common **registration form**.
  - The form includes an **extra checkbox** for specifying whether the new user should be an **Admin**.

## Skill Search
### Description
Logged-in users should be able to search for users based on their **skills**, **countries**, and **cities**.

### Precondition
- The user must be logged in to access the search functionality.

### Narrative
As a registered user of SoA SUT,  
In order to find users with specific skills,  
I want to search by country, city, and skills.

### Acceptance Criteria
- Only logged-in users can search for users.
- Users can search by:
  - **Countries**
  - **Cities**
  - **Skills**
- There is a **drop-down menu** for selecting **available countries**:
  - When a country is selected, the **Available Cities list** updates dynamically with cities from that country.
  - Clicking the **Add** button moves the selected country to the **Countries for Search** area.
- There is a scroll list of available cities where users can select specific cities and add them to the **Cities for Search** area.
- There is a **grid-like list of available skills** with the following columns:
  - **Checkbox** → Allows the user to select skills for the search.
  - **Skill Name** → The name of the skill.
  - **Category** → The main skill category.
  - **Description** → A short description of the skill.
  - **Pagination** → Displays up to **10 skills per page**.
- A **Search button** triggers the search.
- Search results should return **all users** who have the **selected skills** and belong to either the **selected countries** or **selected cities**.
- The search results are displayed in a **grid-like list** with the following columns:
  - **#** → Row number
  - **First Name**
  - **Surname**
  - **Email**
  - **Country**
  - **City**
  - **Skill**
  - **Skill Category**
- The **columns are sortable**.
- **Pagination** is enabled, displaying **10 records per page**.
- There is a **"New Search" button** that allows the user to reset the search and start a new one.

### Note:
- It is expected that in the search results, the same user may appear multiple times, as the results should return **all skills** of that user, even if the search was performed for only **one specific skill**.
