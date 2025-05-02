# EduAI - Educational AI Platform

## Project Overview
EduAI is an educational platform that leverages artificial intelligence to provide personalized learning experiences.

## Tech Stack
- Backend: .NET Core 6.0
- Frontend: HTML, CSS, JavaScript
- Database: SQL Server
- Authentication: JWT
- Email: MailKit
- Logging: Serilog
- Rate Limiting: AspNetCoreRateLimit

## Project Structure
```
EduAI/
├── EduAI.Backend/
│   ├── Controllers/
│   │   └── AuthController.cs
│   ├── Models/
│   │   └── UserModels.cs
│   ├── Services/
│   │   ├── EmailService.cs
│   │   ├── LoggingService.cs
│   │   └── TokenService.cs
│   ├── Program.cs
│   └── appsettings.json
├── frontend/
│   ├── js/
│   │   └── auth.js
│   ├── css/
│   ├── components/
│   └── index.html
└── README.md
```

## Features
- User Authentication (Login/Register)
- Email Verification
- Password Reset
- Two-Factor Authentication
- JWT Token Management
- Rate Limiting
- Logging
- CORS Configuration

## API Endpoints
### Authentication
- POST `/api/auth/login` - User login
- POST `/api/auth/register` - User registration
- POST `/api/auth/verify-2fa` - Two-factor authentication
- POST `/api/auth/verify-email` - Email verification
- POST `/api/auth/forgot-password` - Password reset request
- POST `/api/auth/reset-password` - Password reset

## Configuration
```json
{
  "ConnectionStrings": {
    "DefaultConnection": "Server=localhost;Database=EduAI;Trusted_Connection=True;MultipleActiveResultSets=true"
  },
  "JwtSettings": {
    "Secret": "your-secret-key",
    "Issuer": "EduAI",
    "Audience": "EduAI-Client",
    "ExpiryMinutes": 60,
    "RefreshTokenExpiryDays": 7
  },
  "EmailSettings": {
    "SmtpServer": "smtp.gmail.com",
    "SmtpPort": "587",
    "Username": "your-email@gmail.com",
    "Password": "your-app-password",
    "FromEmail": "noreply@eduai.com",
    "FromName": "EduAI"
  }
}
```

## Installation
1. Clone the repository:
```bash
git clone https://github.com/your-username/EduAI.git
```

2. Install dependencies:
```bash
cd EduAI
dotnet restore
```

3. Configure the database:
```sql
CREATE DATABASE EduAI;
```

4. Update appsettings.json with your configuration

5. Run the application:
```bash
dotnet run
```

## Contributing
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact
Your Name - your.email@example.com
Project Link: [https://github.com/your-username/EduAI](https://github.com/your-username/EduAI) 