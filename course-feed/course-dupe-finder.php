<?php
opcache_reset();
require('../inc/lsapp.php');

// Handle delete action
if (isset($_POST['delete_course']) && isset($_POST['course_id'])) {
    $courseIdToDelete = $_POST['course_id'];
    deleteCourse($courseIdToDelete);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?deleted=1');
    exit;
}

// Path to the CSV file
$csvFile = '../data/courses.csv';

// Array to store courses and duplicates
$allCourses = [];
$duplicateGroups = [];

// Open the CSV file
if (($handle = fopen($csvFile, 'r')) !== false) {
    // Get the headers
    $headers = fgetcsv($handle);
    
    $rowIndex = 0;
    // Process each row
    while (($row = fgetcsv($handle)) !== false) {
        $courseData = array_combine($headers, $row);
        $courseData['_row_index'] = $rowIndex;
        $allCourses[] = $courseData;
        $rowIndex++;
    }
    fclose($handle);
    
    // Group duplicates by lowercase course name + item code
    $courseGroups = [];
    foreach ($allCourses as $course) {
        $uniqueKey = strtolower($course['CourseName']) . '_' . strtolower($course['ItemCode']);
        if (!isset($courseGroups[$uniqueKey])) {
            $courseGroups[$uniqueKey] = [];
        }
        $courseGroups[$uniqueKey][] = $course;
    }
    
    // Find groups with more than one course (duplicates)
    foreach ($courseGroups as $key => $group) {
        if (count($group) > 1) {
            $duplicateGroups[$key] = $group;
        }
    }
    
} else {
    echo "Error: Unable to open the CSV file.\n";
    exit;
}

function deleteCourse($courseId) {
    $csvFile = '../data/courses.csv';
    $tempFile = '../data/courses_temp.csv';
    
    if (($readHandle = fopen($csvFile, 'r')) !== false && ($writeHandle = fopen($tempFile, 'w')) !== false) {
        $headers = fgetcsv($readHandle);
        fputcsv($writeHandle, $headers);
        
        $courseIdIndex = array_search('CourseID', $headers);
        
        while (($row = fgetcsv($readHandle)) !== false) {
            if ($row[$courseIdIndex] !== $courseId) {
                fputcsv($writeHandle, $row);
            }
        }
        
        fclose($readHandle);
        fclose($writeHandle);
        
        rename($tempFile, $csvFile);
    }
}

function highlightDifferences($text1, $text2) {
    if ($text1 === $text2) {
        return htmlspecialchars($text1);
    }
    return '<span style="background-color: #ffeb3b; padding: 2px 4px; border-radius: 3px;">' . htmlspecialchars($text1) . '</span>';
}

function getImportantFields() {
    return [
        'CourseID' => 'Course ID',
        'Status' => 'Status', 
        'CourseName' => 'Course Name',
        'ItemCode' => 'Item Code',
        'Method' => 'Delivery Method',
        'Platform' => 'Platform',
        'LearningHubPartner' => 'Learning Partner',
        'HUBInclude' => 'HUB Include',
        'Requested' => 'Date Requested',
        'Modified' => 'Date Modified'
    ];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Duplicate Finder</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <style>
        .course-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
        }
        .duplicate-group {
            border: 2px solid #dc3545;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            background: #fff;
        }
        .field-diff {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 5px 10px;
            margin: 2px 0;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <h1 class="mt-4 mb-4">Course Duplicate Finder</h1>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Course deleted successfully!</div>
    <?php endif; ?>
    
    <?php if (empty($duplicateGroups)): ?>
        <div class="alert alert-success">
            <h4>No duplicates found!</h4>
            <p>All courses have unique combinations of course name and item code (case-insensitive).</p>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <h4><?= count($duplicateGroups) ?> duplicate group(s) found</h4>
            <p>The following courses have the same name and item code (case-insensitive):</p>
        </div>
        
        <?php foreach ($duplicateGroups as $groupKey => $group): ?>
            <div class="duplicate-group">
                <h3 class="text-danger mb-3">Duplicate Group: <?= htmlspecialchars($group[0]['CourseName']) ?></h3>
                
                <div class="row">
                    <?php foreach ($group as $index => $course): ?>
                        <div class="col-md-6">
                            <div class="course-card">
                                <h5>Course <?= $index + 1 ?> 
                                    <span class="badge bg-<?= $course['Status'] === 'Active' ? 'success' : 'secondary' ?>">
                                        <?= $course['Status'] ?>
                                    </span>
                                </h5>
                                
                                <?php $importantFields = getImportantFields(); ?>
                                <?php foreach ($importantFields as $field => $label): ?>
                                    <?php if (isset($course[$field])): ?>
                                        <div class="mb-2">
                                            <strong><?= $label ?>:</strong>
                                            <?php 
                                            // Check if this field differs from other courses in the group
                                            $isDifferent = false;
                                            foreach ($group as $otherCourse) {
                                                if ($otherCourse['CourseID'] !== $course['CourseID'] && $otherCourse[$field] !== $course[$field]) {
                                                    $isDifferent = true;
                                                    break;
                                                }
                                            }
                                            ?>
                                            <?php if ($isDifferent): ?>
                                                <div class="field-diff">
                                                    <?= highlightDifferences($course[$field], '') ?>
                                                </div>
                                            <?php else: ?>
                                                <?= htmlspecialchars($course[$field]) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                
                                <div class="mt-3">
                                    <form method="post" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                                        <input type="hidden" name="course_id" value="<?= htmlspecialchars($course['CourseID']) ?>">
                                        <button type="submit" name="delete_course" class="btn btn-delete btn-sm">
                                            🗑️ Delete This Course
                                        </button>
                                    </form>
                                    <a href="../course.php?courseid=<?= htmlspecialchars($course['CourseID']) ?>" 
                                       class="btn btn-outline-primary btn-sm" target="_blank">
                                        👁️ View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <h6>📊 Comparison Summary:</h6>
                    <?php 
                    $fieldDiffs = [];
                    foreach (getImportantFields() as $field => $label) {
                        $values = array_unique(array_column($group, $field));
                        if (count($values) > 1) {
                            $fieldDiffs[] = $label;
                        }
                    }
                    ?>
                    <?php if (!empty($fieldDiffs)): ?>
                        <p><strong>Fields that differ:</strong> <?= implode(', ', $fieldDiffs) ?></p>
                    <?php else: ?>
                        <p class="text-success">All important fields are identical - these might be true duplicates.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="../courses.php" class="btn btn-secondary">← Back to Courses</a>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-primary">🔄 Refresh</a>
    </div>
</div>

<script src="../js/bootstrap.min.js"></script>
</body>
</html>