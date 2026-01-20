CREATE DATABASE school_management_system;
USE school_management_system;

-- Tabela e Userave(Admin/Student)
CREATE TABLE Users(
    UsersID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('Admin', 'Student') NOT NULL DEFAULT 'Student',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela e Grupeve

CREATE TABLE Groups(
    GroupID INT AUTO_INCREMENT PRIMARY KEY,
    GroupName VARCHAR(100) NOT NULL,
    Description TEXT
);

-- Tabela e Subjects ose Lendeve

CREATE TABLE Subjects(
    SubjectID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Description TEXT
);

-- Tabela e Kontaktit (Mesazhet nga forma e kontaktit)

CREATE TABLE Contact_Messages(
    CM_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Message TEXT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Shto nje kolone per te shenuar nese mesazhi eshte lexuar
ALTER TABLE contact_messages ADD COLUMN IsRead TINYINT(1) DEFAULT 0;

-- Tabela Student_Groups (Lidh studentet me grupet - Many-to-Many)

CREATE TABLE Student_Groups(
    SG_ID INT AUTO_INCREMENT PRIMARY KEY,
    UsersID INT NOT NULL,
    GroupID INT NOT NULL,
    FOREIGN KEY (UsersID) REFERENCES Users(UsersID) ON DELETE CASCADE,
    FOREIGN KEY (GroupID) REFERENCES Groups(GroupID) ON DELETE CASCADE,
    UNIQUE KEY unique_student_group (UsersID, GroupID)
);

-- Tabela e Lessons (Materialet mesimore)

CREATE TABLE Lessons(
    LessonID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(200) NOT NULL,
    Description TEXT,
    UploadTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UsersID INT NOT NULL,
    GroupID INT,
    SubjectID INT,
    FOREIGN KEY (UsersID) REFERENCES Users(UsersID) ON DELETE CASCADE,
    FOREIGN KEY (GroupID) REFERENCES Groups(GroupID) ON DELETE SET NULL,
    FOREIGN KEY (SubjectID) REFERENCES Subjects(SubjectID) ON DELETE SET NULL
);

-- Tabela e Documents (Dokumentet shtese)

CREATE TABLE Documents(
    DocumentsID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(200) NOT NULL,
    FilePath VARCHAR(255),
    UploadedBy INT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UploadedBy) REFERENCES Users(UsersID) ON DELETE CASCADE
);

-- Tabela e Schedules (Orari per cdo grup)

CREATE TABLE Schedules(
    ScheduleID INT AUTO_INCREMENT PRIMARY KEY,
    GroupID INT NOT NULL,
    SubjectID INT,
    Day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday') NOT NULL,
    StartTime TIME NOT NULL,
    EndTime TIME NOT NULL,
    Klasa VARCHAR(50),
    FOREIGN KEY (GroupID) REFERENCES Groups(GroupID) ON DELETE CASCADE,
    FOREIGN KEY (SubjectID) REFERENCES Subjects(SubjectID) ON DELETE SET NULL
);

-- Tabela e Assignments (Detyrat per studentet)

CREATE TABLE Assignments(
    AssignmentID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(200) NOT NULL,
    Description TEXT,
    DueDate DATE NOT NULL,
    LessonID INT,
    GroupID INT NOT NULL,
    SubjectID INT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (LessonID) REFERENCES Lessons(LessonID) ON DELETE SET NULL,
    FOREIGN KEY (GroupID) REFERENCES Groups(GroupID) ON DELETE CASCADE,
    FOREIGN KEY (SubjectID) REFERENCES Subjects(SubjectID) ON DELETE SET NULL
);

-- Tabela e Submissions (Dorezimet e detyrave nga studentet)

CREATE TABLE Submissions(
    SubmissionID INT AUTO_INCREMENT PRIMARY KEY,
    AssignmentID INT NOT NULL,
    UsersID INT NOT NULL,
    SubmittedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Grade DECIMAL(5,2),
    Status ENUM('Pending', 'Submitted', 'Graded') NOT NULL DEFAULT 'Pending',
    FilePath VARCHAR(255),
    FOREIGN KEY (AssignmentID) REFERENCES Assignments(AssignmentID) ON DELETE CASCADE,
    FOREIGN KEY (UsersID) REFERENCES Users(UsersID) ON DELETE CASCADE,
    UNIQUE KEY unique_submission (AssignmentID, UsersID)
);

-- Tabela e Attendance (Prezenca e studenteve)

CREATE TABLE Attendance(
    AttendanceID INT AUTO_INCREMENT PRIMARY KEY,
    UsersID INT NOT NULL,
    ScheduleID INT NOT NULL,
    Date DATE NOT NULL,
    Status ENUM('Present', 'Absent') NOT NULL DEFAULT 'Present',
    FOREIGN KEY (UsersID) REFERENCES Users(UsersID) ON DELETE CASCADE,
    FOREIGN KEY (ScheduleID) REFERENCES Schedules(ScheduleID) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (UsersID, ScheduleID, Date)
);

-- Tabela e Reviews (Vleresimet e studenteve per mesimet)

CREATE TABLE Reviews(
    ReviewsID INT AUTO_INCREMENT PRIMARY KEY,
    UsersID INT NOT NULL,
    LessonID INT NOT NULL,
    Comment TEXT,
    Rating INT CHECK (Rating >= 1 AND Rating <= 5),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UsersID) REFERENCES Users(UsersID) ON DELETE CASCADE,
    FOREIGN KEY (LessonID) REFERENCES Lessons(LessonID) ON DELETE CASCADE,
    UNIQUE KEY unique_review (UsersID, LessonID)
);


