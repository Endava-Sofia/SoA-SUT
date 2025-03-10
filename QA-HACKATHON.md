# QA Hackathon: AI Challenge

## Overview
Welcome to our Time-Boxed QA Hackathon! In this event, you'll be challenged to experiment with various AI-powered code editors to accomplish specific tasks in a short, focused timeframe. By exploring different AI tools, you’ll gain new insights and techniques to enhance your QA and development skills.

## Repository
We’ve set up a GitHub repository containing a web application with user management capabilities:

[Endava-Sofia/SoA-SUT](https://github.com/Endava-Sofia/SoA-SUT/tree/main)

This application consists of:
- **PHP frontend**
- **Python REST API**
- **MySQL database**

It is recommended to deploy and run the app locally, but it is also accessible on the web for reference:
- Web UI: https://www.proudgrass-870f4033.westeurope.azurecontainerapps.io/login.php
- REST API: https://rest.proudgrass-870f4033.westeurope.azurecontainerapps.io
- MySQL Database: mysql.proudgrass-870f4033.westeurope.azurecontainerapps.io:3306
- *Azure hosted application goes into sleep mode when not used, first request might fail until it is awaken.

Application guide can be found [here](https://github.com/Endava-Sofia/SoA-SUT/blob/main/README.md).

## Application Features
[SoA SUT Web Application Requirements Document](https://github.com/Endava-Sofia/SoA-SUT/blob/main/FEATURES.md)

## Objectives
1. **Evaluate the Code and Fix Bugs**  
   The application itself contains some bugs and glitches.
   Use an AI-based code editor to review the existing code, identify bugs, and provide fixes.
   You can select from any AI coding tools:
   - ChatGPT Enterprise ( *optional [Repomix](https://github.com/yamadashy/repomix) )
   - [GitHub Copilot for IDEs](https://github.com/features/copilot)
   - [Cursor IDE free](https://www.cursor.com/pricing)
   - [Windsurf IDE free](https://codeium.com/windsurf)
   - [Augment Code IDE free](https://www.augmentcode.com/pricing)
   - [Gemini Code Assist for IDEs](https://codeassist.google/)
   - [Cline for VSCode](https://cline.bot/) - (https://openrouter.ai/ free account is required)
   
   Hint: Additionally use your own QA magic skills to find some of the issues by manually testing the application and then use the AI to describe and fix them.

2. **Implement a New Feature**  
   Extend the capabilities of the application with a new feature.

    - **User Profile Enhancement**
    The current user profile system only allows users to edit their title, name, country, and city. Some ideas to extend this functionality include:
        - Profile Picture Upload: Add a feature for users to upload and manage a profile picture.
        - Implement image validation (size, type, dimensions)
        - Store images in a dedicated directory with proper naming conventions
        - Display the profile picture in the navbar, profile page, and user listings

3. **(Optional) Unit Tests, Integration Tests**  
   Add unit or API integration tests for any functionality you implement or fix, showcasing how AI tools can streamline testing.

4. **Create Pull Requests**  
   Submit your changes via Pull Requests (PRs).

## Documentation Requirements
Along with your PR, include a single Markdown text file (e.g. SOLUTION.md) containing:
- **Which AI tool(s) were used**
- **How you prompted the AI** (describe your approach)
- **Challenges encountered**
- **How you overcame limitations** of the AI tool(s)

## Support and Issues
If you have any questions or need further details, feel free to reach out:
*yakim.rachev@endava.com* | *georgi.kokonov@endava.com* | *some random collegue*

---

**Good luck, and have fun experimenting!**

> **Tip:** Be creative with the AI prompts you use — the more clearly you communicate your goals to the AI, the better your results will be.
