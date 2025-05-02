-- Create Users table
CREATE TABLE Users (
    UserId INT IDENTITY(1,1) PRIMARY KEY,
    Email NVARCHAR(100) UNIQUE NOT NULL,
    Password NVARCHAR(100) NOT NULL,
    FullName NVARCHAR(100) NOT NULL,
    Role NVARCHAR(20) DEFAULT 'student',
    Avatar NVARCHAR(255),
    CreatedAt DATETIME DEFAULT GETDATE(),
    LastLogin DATETIME,
    IsActive BIT DEFAULT 1
);

-- Create Courses table
CREATE TABLE Courses (
    CourseId INT IDENTITY(1,1) PRIMARY KEY,
    Title NVARCHAR(200) NOT NULL,
    Description NVARCHAR(MAX),
    Thumbnail NVARCHAR(255),
    CreatedBy INT FOREIGN KEY REFERENCES Users(UserId),
    CreatedAt DATETIME DEFAULT GETDATE(),
    UpdatedAt DATETIME,
    IsActive BIT DEFAULT 1
);

-- Create Lessons table
CREATE TABLE Lessons (
    LessonId INT IDENTITY(1,1) PRIMARY KEY,
    CourseId INT FOREIGN KEY REFERENCES Courses(CourseId),
    Title NVARCHAR(200) NOT NULL,
    Content NVARCHAR(MAX),
    VideoUrl NVARCHAR(255),
    OrderNumber INT,
    CreatedAt DATETIME DEFAULT GETDATE(),
    UpdatedAt DATETIME
);

-- Create UserCourses (Enrollment) table
CREATE TABLE UserCourses (
    UserId INT FOREIGN KEY REFERENCES Users(UserId),
    CourseId INT FOREIGN KEY REFERENCES Courses(CourseId),
    EnrolledAt DATETIME DEFAULT GETDATE(),
    Progress INT DEFAULT 0,
    PRIMARY KEY (UserId, CourseId)
);

-- Create Quizzes table
CREATE TABLE Quizzes (
    QuizId INT IDENTITY(1,1) PRIMARY KEY,
    LessonId INT FOREIGN KEY REFERENCES Lessons(LessonId),
    Title NVARCHAR(200) NOT NULL,
    Description NVARCHAR(MAX),
    TimeLimit INT, -- in minutes
    CreatedAt DATETIME DEFAULT GETDATE()
);

-- Create Questions table
CREATE TABLE Questions (
    QuestionId INT IDENTITY(1,1) PRIMARY KEY,
    QuizId INT FOREIGN KEY REFERENCES Quizzes(QuizId),
    QuestionText NVARCHAR(MAX) NOT NULL,
    QuestionType NVARCHAR(20) NOT NULL, -- multiple_choice, true_false, short_answer
    Points INT DEFAULT 1,
    OrderNumber INT
);

-- Create Answers table
CREATE TABLE Answers (
    AnswerId INT IDENTITY(1,1) PRIMARY KEY,
    QuestionId INT FOREIGN KEY REFERENCES Questions(QuestionId),
    AnswerText NVARCHAR(MAX) NOT NULL,
    IsCorrect BIT DEFAULT 0,
    OrderNumber INT
);

-- Create UserQuizAttempts table
CREATE TABLE UserQuizAttempts (
    AttemptId INT IDENTITY(1,1) PRIMARY KEY,
    UserId INT FOREIGN KEY REFERENCES Users(UserId),
    QuizId INT FOREIGN KEY REFERENCES Quizzes(QuizId),
    StartedAt DATETIME DEFAULT GETDATE(),
    CompletedAt DATETIME,
    Score INT,
    TotalQuestions INT
);

-- Create UserAnswers table
CREATE TABLE UserAnswers (
    UserAnswerId INT IDENTITY(1,1) PRIMARY KEY,
    AttemptId INT FOREIGN KEY REFERENCES UserQuizAttempts(AttemptId),
    QuestionId INT FOREIGN KEY REFERENCES Questions(QuestionId),
    AnswerId INT FOREIGN KEY REFERENCES Answers(AnswerId),
    AnswerText NVARCHAR(MAX), -- for short answers
    IsCorrect BIT
);

-- Create AIInteractions table
CREATE TABLE AIInteractions (
    InteractionId INT IDENTITY(1,1) PRIMARY KEY,
    UserId INT FOREIGN KEY REFERENCES Users(UserId),
    InteractionType NVARCHAR(50) NOT NULL, -- chat, tutor, 3d_model
    InputText NVARCHAR(MAX),
    OutputText NVARCHAR(MAX),
    CreatedAt DATETIME DEFAULT GETDATE()
);

-- Create Notifications table
CREATE TABLE Notifications (
    NotificationId INT IDENTITY(1,1) PRIMARY KEY,
    UserId INT FOREIGN KEY REFERENCES Users(UserId),
    Title NVARCHAR(200) NOT NULL,
    Message NVARCHAR(MAX) NOT NULL,
    IsRead BIT DEFAULT 0,
    CreatedAt DATETIME DEFAULT GETDATE()
);

-- Insert default admin user
INSERT INTO Users (Email, Password, FullName, Role)
VALUES ('admin@eduai.com', 'admin123', 'System Administrator', 'admin'); 