FROM python:3.9-slim

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    default-libmysqlclient-dev \
    gcc \
    pkg-config \
    python3-dev \
    && rm -rf /var/lib/apt/lists/*

# Copy the entire API directory
COPY . /app/

# Install requirements
RUN pip install --no-cache-dir -r requirements.txt

# Set environment variables for testing
ENV MYSQL_HOST=db
ENV MYSQL_USER=root
ENV MYSQL_PASSWORD=pass
ENV MYSQL_DB=db
ENV PYTHONPATH=/app

# Command to run tests with arguments
ENTRYPOINT ["pytest", "tests/test_profile_picture.py"]
CMD ["-v"] 