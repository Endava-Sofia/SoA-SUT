from app import create_app

app = create_app()

if __name__ == "__main__":
    print("Starting application")
    app.run(host='0.0.0.0')
    print("Application is started")