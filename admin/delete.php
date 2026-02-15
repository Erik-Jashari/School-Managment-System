<?php 
    include '../config/database.php';

    if (isset($_GET['UsersID'])) {
        $id = $_GET['UsersID'];

        $sql = "DELETE FROM users WHERE UsersID=$id";
        $connection->query($sql);
        header("Location: users.php");
    } elseif (isset($_GET['CM_ID'])) {
        $id = $_GET['CM_ID'];

        $sql = "DELETE FROM contact_messages WHERE CM_ID=$id";
        $connection->query($sql);
        header("Location: contactMessages.php");
    } elseif (isset($_GET['ReviewsID'])) {
        $id = $_GET['ReviewsID'];

        $sql = "DELETE FROM Reviews WHERE ReviewsID=$id";
        $connection->query($sql);
        header("Location: reviews.php");
    } elseif (isset($_GET['GroupID'])) {
        $id = $_GET['GroupID'];

        $sql = "DELETE FROM Groups WHERE GroupID=$id";
        $connection->query($sql);
        header("Location: groups.php");
    } elseif (isset($_GET['SubjectID'])) {
        $id = $_GET['SubjectID'];

        $sql = "DELETE FROM Subjects WHERE SubjectID=$id";
        $connection->query($sql);
        header("Location: subjects.php");
    } elseif (isset($_GET['SubmissionID'])) {
        $id = $_GET['SubmissionID'];

        $sql = "DELETE FROM Submissions WHERE SubmissionID=$id";
        $connection->query($sql);
        header("Location: submissions.php");
    } elseif (isset($_GET['LessonID'])) {
        $id = $_GET['LessonID'];

        $sql = "DELETE FROM Lessons WHERE LessonID=$id";
        $connection->query($sql);
        header("Location: lessons.php");
    } elseif (isset($_GET['AssignmentID'])) {
        $id = $_GET['AssignmentID'];

        $sql = "DELETE FROM Assignments WHERE AssignmentID=$id";
        $connection->query($sql);
        header("Location: assignments.php");
    }

    exit;
?>