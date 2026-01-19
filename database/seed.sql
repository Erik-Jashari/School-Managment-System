USE school_management_system;

-- =============================================
-- USERS (1 Admin + 10 Students)
-- Password for all: "Test123!" (hashed with PHP password_hash)
-- =============================================

INSERT INTO Users (Name, Email, Password, Role) VALUES
-- Admin
('Admin User', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'),

-- Students
('Arta Berisha', 'arta.berisha@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Blend Krasniqi', 'blend.krasniqi@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Dea Gashi', 'dea.gashi@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Eron Hoxha', 'eron.hoxha@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Fjolla Mustafa', 'fjolla.mustafa@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Gent Rama', 'gent.rama@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Hana Shala', 'hana.shala@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Ilir Morina', 'ilir.morina@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Jeta Ahmeti', 'jeta.ahmeti@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Kushtrim Beqiri', 'kushtrim.beqiri@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student');

-- =============================================
-- GROUPS (4 class groups)
-- =============================================

INSERT INTO Groups (GroupName, Description) VALUES
('Klasa 10-A', 'Klasa e 10-te, drejtimi shkencor me fokus ne matematike dhe fizike'),
('Klasa 10-B', 'Klasa e 10-te, drejtimi i pergjithshem'),
('Klasa 11-A', 'Klasa e 11-te, drejtimi shkencor me fokus ne biologji dhe kimi'),
('Klasa 11-B', 'Klasa e 11-te, drejtimi shoqeror me fokus ne histori dhe gjuhe');

-- =============================================
-- SUBJECTS (6 subjects)
-- =============================================

INSERT INTO Subjects (Name, Description) VALUES
('Matematike', 'Algjebra, gjeometria, dhe kalkulusi per shkollen e mesme'),
('Fizike', 'Mekanika, termodinamika, dhe elektromagnetizmi'),
('Kimi', 'Kimia organike dhe inorganike'),
('Biologji', 'Biologjia qelizore, gjenetika, dhe ekologjia'),
('Gjuhe Angleze', 'Gramatika, literatura, dhe shkrimi akademik'),
('Histori', 'Historia boterore dhe historia e Kosoves');

-- =============================================
-- STUDENT_GROUPS (Assign students to groups)
-- Students 2-6 in Klasa 10-A, Students 7-11 in Klasa 10-B
-- =============================================

INSERT INTO Student_Groups (UsersID, GroupID) VALUES
-- Klasa 10-A (5 students)
(2, 1),  -- Arta -> 10-A
(3, 1),  -- Blend -> 10-A
(4, 1),  -- Dea -> 10-A
(5, 1),  -- Eron -> 10-A
(6, 1),  -- Fjolla -> 10-A

-- Klasa 10-B (5 students)
(7, 2),  -- Gent -> 10-B
(8, 2),  -- Hana -> 10-B
(9, 2),  -- Ilir -> 10-B
(10, 2), -- Jeta -> 10-B
(11, 2); -- Kushtrim -> 10-B

-- =============================================
-- LESSONS (3-4 lessons per subject, uploaded by Admin)
-- =============================================

INSERT INTO Lessons (Title, Description, UsersID, GroupID, SubjectID) VALUES
-- Matematike (SubjectID = 1)
('Hyrje ne Algjeber', 'Konceptet themelore te algjebres: variablat, shprehjet, dhe ekuacionet', 1, 1, 1),
('Ekuacionet Lineare', 'Zgjidhja e ekuacioneve lineare me nje variabel', 1, 1, 1),
('Funksionet Kuadratike', 'Grafiku dhe vetite e funksioneve kuadratike', 1, 1, 1),
('Gjeometria Baze', 'Kendet, trekëndeshat, dhe teorema e Pitagores', 1, 2, 1),

-- Fizike (SubjectID = 2)
('Ligjet e Njutonit', 'Tre ligjet e levizjes se Njutonit me shembuj praktik', 1, 1, 2),
('Energjia dhe Puna', 'Konceptet e energjise kinetike dhe potenciale', 1, 1, 2),
('Valet dhe Zeri', 'Karakteristikat e valeve dhe fenomenet e zerit', 1, 2, 2),

-- Kimi (SubjectID = 3)
('Struktura Atomike', 'Protonet, neutronet, elektronet dhe modeli atomik', 1, 1, 3),
('Tabela Periodike', 'Organizimi i elementeve dhe vetite periodike', 1, 1, 3),
('Lidhjet Kimike', 'Lidhjet jonike, kovalente, dhe metalike', 1, 2, 3),

-- Biologji (SubjectID = 4)
('Qeliza', 'Struktura dhe funksionet e qelizes', 1, 1, 4),
('ADN dhe Gjenetika', 'Struktura e ADN-se dhe trashegimia gjenetike', 1, 1, 4),
('Ekosistemi', 'Marredheniet midis organizmave dhe mjedisit', 1, 2, 4),

-- Gjuhe Angleze (SubjectID = 5)
('English Grammar Basics', 'Parts of speech, sentence structure, and tenses', 1, 1, 5),
('Essay Writing', 'How to write academic essays with proper structure', 1, 1, 5),
('Reading Comprehension', 'Strategies for understanding complex texts', 1, 2, 5),

-- Histori (SubjectID = 6)
('Lufta e Dyte Boterore', 'Shkaqet, ngjarjet kryesore, dhe pasojat e LDB', 1, 1, 6),
('Pavaresia e Kosoves', 'Rrugetimi drejt pavaresise se Kosoves', 1, 1, 6),
('Perandoria Osmane', 'Historia e Perandorise Osmane ne Ballkan', 1, 2, 6);

-- =============================================
-- SCHEDULES (Weekly timetable for Klasa 10-A and 10-B)
-- =============================================

INSERT INTO Schedules (GroupID, SubjectID, Day, StartTime, EndTime, Klasa) VALUES
-- Klasa 10-A Schedule
(1, 1, 'Monday', '08:00:00', '09:30:00', 'A101'),    -- Matematike
(1, 2, 'Monday', '09:45:00', '11:15:00', 'A102'),    -- Fizike
(1, 5, 'Monday', '11:30:00', '13:00:00', 'A101'),    -- Anglisht

(1, 3, 'Tuesday', '08:00:00', '09:30:00', 'Lab-1'),  -- Kimi
(1, 4, 'Tuesday', '09:45:00', '11:15:00', 'Lab-2'),  -- Biologji
(1, 6, 'Tuesday', '11:30:00', '13:00:00', 'A101'),   -- Histori

(1, 1, 'Wednesday', '08:00:00', '09:30:00', 'A101'), -- Matematike
(1, 2, 'Wednesday', '09:45:00', '11:15:00', 'A102'), -- Fizike
(1, 5, 'Wednesday', '11:30:00', '13:00:00', 'A101'), -- Anglisht

(1, 3, 'Thursday', '08:00:00', '09:30:00', 'Lab-1'), -- Kimi
(1, 1, 'Thursday', '09:45:00', '11:15:00', 'A101'),  -- Matematike
(1, 6, 'Thursday', '11:30:00', '13:00:00', 'A101'),  -- Histori

(1, 4, 'Friday', '08:00:00', '09:30:00', 'Lab-2'),   -- Biologji
(1, 2, 'Friday', '09:45:00', '11:15:00', 'A102'),    -- Fizike
(1, 5, 'Friday', '11:30:00', '13:00:00', 'A101'),    -- Anglisht

-- Klasa 10-B Schedule
(2, 1, 'Monday', '08:00:00', '09:30:00', 'B101'),    -- Matematike
(2, 5, 'Monday', '09:45:00', '11:15:00', 'B101'),    -- Anglisht
(2, 6, 'Monday', '11:30:00', '13:00:00', 'B102'),    -- Histori

(2, 2, 'Tuesday', '08:00:00', '09:30:00', 'B102'),   -- Fizike
(2, 3, 'Tuesday', '09:45:00', '11:15:00', 'Lab-1'),  -- Kimi
(2, 4, 'Tuesday', '11:30:00', '13:00:00', 'Lab-2'),  -- Biologji

(2, 1, 'Wednesday', '08:00:00', '09:30:00', 'B101'), -- Matematike
(2, 6, 'Wednesday', '09:45:00', '11:15:00', 'B102'), -- Histori
(2, 5, 'Wednesday', '11:30:00', '13:00:00', 'B101'), -- Anglisht

(2, 2, 'Thursday', '08:00:00', '09:30:00', 'B102'),  -- Fizike
(2, 4, 'Thursday', '09:45:00', '11:15:00', 'Lab-2'), -- Biologji
(2, 1, 'Thursday', '11:30:00', '13:00:00', 'B101'),  -- Matematike

(2, 3, 'Friday', '08:00:00', '09:30:00', 'Lab-1'),   -- Kimi
(2, 6, 'Friday', '09:45:00', '11:15:00', 'B102'),    -- Histori
(2, 5, 'Friday', '11:30:00', '13:00:00', 'B101');    -- Anglisht

-- =============================================
-- ASSIGNMENTS (Various assignments for both groups)
-- =============================================

INSERT INTO Assignments (Title, Description, DueDate, LessonID, GroupID, SubjectID) VALUES
-- Matematike assignments
('Detyra 1: Algjebra', 'Zgjidh ushtrimet 1-10 nga libri, faqe 45', '2026-01-15', 1, 1, 1),
('Detyra 2: Ekuacionet', 'Zgjidh 5 ekuacione lineare', '2026-01-20', 2, 1, 1),
('Quiz: Gjeometria', 'Quiz online per gjeometrine baze', '2026-01-18', 4, 2, 1),

-- Fizike assignments
('Laborator: Ligjet e Njutonit', 'Kryeni eksperimentin dhe shkruani raportin', '2026-01-22', 5, 1, 2),
('Detyra: Energjia', 'Llogaritni energjine kinetike per 5 situata', '2026-01-25', 6, 1, 2),

-- Kimi assignments
('Projekt: Modeli Atomik', 'Krijoni nje model 3D te atomit', '2026-01-28', 8, 1, 3),
('Detyra: Tabela Periodike', 'Plotesoni tabelen me 20 elemente', '2026-01-17', 9, 1, 3),

-- Biologji assignments
('Ese: Qeliza', 'Shkruani nje ese 500 fjale per qelizen', '2026-01-19', 11, 1, 4),
('Laborator: Mikroskopi', 'Vizatoni qelizat qe shihni ne mikroskop', '2026-01-23', 11, 2, 4),

-- Anglisht assignments
('Essay: My Future', 'Write a 300-word essay about your future plans', '2026-01-16', 15, 1, 5),
('Grammar Exercise', 'Complete exercises on past tense', '2026-01-14', 14, 2, 5),

-- Histori assignments
('Hulumtim: LDB', 'Hulumtoni per nje ngjarje te LDB dhe prezantoni', '2026-01-30', 17, 1, 6),
('Ese: Pavaresia', 'Shkruani per rendesine e pavaresise se Kosoves', '2026-01-27', 18, 1, 6);

-- =============================================
-- SUBMISSIONS (Sample student submissions)
-- =============================================

INSERT INTO Submissions (AssignmentID, UsersID, SubmittedAt, Grade, Status, FilePath) VALUES
-- Assignment 1 (Algjebra) - Most students submitted
(1, 2, '2026-01-14 10:30:00', 85.00, 'Graded', 'uploads/arta_algjebra.pdf'),
(1, 3, '2026-01-14 14:20:00', 92.00, 'Graded', 'uploads/blend_algjebra.pdf'),
(1, 4, '2026-01-15 08:00:00', 78.00, 'Graded', 'uploads/dea_algjebra.pdf'),
(1, 5, '2026-01-13 16:45:00', 88.00, 'Graded', 'uploads/eron_algjebra.pdf'),
(1, 6, '2026-01-14 20:00:00', NULL, 'Submitted', 'uploads/fjolla_algjebra.pdf'),

-- Assignment 10 (English Essay) - Some submitted
(10, 2, '2026-01-15 09:00:00', 90.00, 'Graded', 'uploads/arta_essay.pdf'),
(10, 3, '2026-01-15 11:30:00', NULL, 'Submitted', 'uploads/blend_essay.pdf'),
(10, 5, '2026-01-14 22:00:00', 95.00, 'Graded', 'uploads/eron_essay.pdf'),

-- Assignment 11 (Grammar for 10-B)
(11, 7, '2026-01-13 15:00:00', 82.00, 'Graded', NULL),
(11, 8, '2026-01-14 10:00:00', 88.00, 'Graded', NULL),
(11, 9, '2026-01-13 18:30:00', NULL, 'Submitted', NULL),

-- Assignment 8 (Biology Essay) - Pending submissions
(8, 2, '2026-01-18 14:00:00', NULL, 'Submitted', 'uploads/arta_qeliza.pdf'),
(8, 4, '2026-01-18 16:30:00', NULL, 'Submitted', 'uploads/dea_qeliza.pdf');

-- =============================================
-- ATTENDANCE (Sample attendance for this week)
-- =============================================

INSERT INTO Attendance (UsersID, ScheduleID, Date, Status) VALUES
-- Monday attendance for Klasa 10-A (ScheduleID 1, 2, 3)
(2, 1, '2026-01-06', 'Present'),
(3, 1, '2026-01-06', 'Present'),
(4, 1, '2026-01-06', 'Absent'),
(5, 1, '2026-01-06', 'Present'),
(6, 1, '2026-01-06', 'Present'),

(2, 2, '2026-01-06', 'Present'),
(3, 2, '2026-01-06', 'Present'),
(4, 2, '2026-01-06', 'Absent'),
(5, 2, '2026-01-06', 'Present'),
(6, 2, '2026-01-06', 'Present'),

-- Tuesday attendance for Klasa 10-A
(2, 4, '2026-01-07', 'Present'),
(3, 4, '2026-01-07', 'Present'),
(4, 4, '2026-01-07', 'Present'),
(5, 4, '2026-01-07', 'Absent'),
(6, 4, '2026-01-07', 'Present');

-- =============================================
-- REVIEWS (Student reviews for lessons)
-- =============================================

INSERT INTO Reviews (UsersID, LessonID, Comment, Rating) VALUES
(2, 1, 'Shume e qarte dhe e lehte per tu kuptuar!', 5),
(3, 1, 'Shembujt ishin te mire, por doja me shume ushtrime', 4),
(4, 5, 'Eksperimentet e bejne fiziken interesante', 5),
(5, 8, 'Kimia eshte e veshtire por mesimi ndihmoi shume', 4),
(6, 11, 'Me pelqeu shume tema e qelizes', 5),
(7, 4, 'Gjeometria eshte e lehte me kete mesim', 4),
(8, 7, 'Valet jane interesante!', 5);

-- =============================================
-- CONTACT MESSAGES (Sample messages)
-- =============================================

INSERT INTO Contact_Messages (Name, Email, Message) VALUES
('Prindi i Artes', 'prind.berisha@gmail.com', 'Pershendetje, doja te dija per orarin e provimeve te javes se ardhshme.'),
('Fatmir Gashi', 'fatmir.gashi@gmail.com', 'A mund te me tregoni si mund te regjistroj femijen tim ne shkolle?'),
('Laura Morina', 'laura.m@hotmail.com', 'Jam e interesuar per programin e burses per studentet e shkellqyer.');

-- =============================================
-- DOCUMENTS (Sample documents)
-- =============================================

INSERT INTO Documents (Title, FilePath, UploadedBy) VALUES
('Syllabus Matematike 2026', 'documents/syllabus_math_2026.pdf', 1),
('Rregullat e Shkolles', 'documents/school_rules.pdf', 1),
('Kalendari Akademik', 'documents/academic_calendar_2026.pdf', 1),
('Formulat e Fizikes', 'documents/physics_formulas.pdf', 1);
