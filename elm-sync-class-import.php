<?php
/**
 * ELM-LSApp Class Import
 * 
 * This script imports new classes from ELM that exist in the courses.csv
 * but are not yet in classes.csv.
 */

require('inc/lsapp.php');

class ElmClassImporter {
    private $elmFilePath = 'data/elm.csv';
    private $classesFilePath = 'data/classes.csv';
    private $coursesFilePath = 'data/courses.csv';
    private $backupDir = 'data/backups/';
    
    private $elmHeaders;
    private $classesHeaders;
    private $lsappClasses = [];
    private $lsappCourses = [];
    private $importedCount = 0;
    private $importedClasses = [];
    
    /**
     * Initialize the import process
     */
    public function initialize() {
        if (!$this->createBackups()) {
            return false;
        }
        
        $this->loadLsappClasses();
        $this->loadLsappCourses();
        return true;
    }
    
    /**
     * Create backup files before processing
     * 
     * @return bool True if backups were successful, false otherwise
     */
    private function createBackups() {
        $timestamp = date('Ymd\THis');
        
        $classesBackupFile = $this->backupDir . 'classes' . $timestamp . '.csv';
        if (!copy($this->classesFilePath, $classesBackupFile)) {
            echo "Failed to backup $classesBackupFile...\nPlease inform the Team Lead ASAP";
            return false;
        }
        
        return true;
    }
    
    /**
     * Load LSApp classes from CSV file
     */
    private function loadLsappClasses() {
        $lsapp = fopen($this->classesFilePath, 'r');
        $this->classesHeaders = fgetcsv($lsapp);
        
        while ($row = fgetcsv($lsapp)) {
            // Store ItemCode as the key for easy lookup
            $this->lsappClasses[$row[7]] = $row;
        }
        
        fclose($lsapp);
    }
    
    /**
     * Load LSApp courses from CSV file
     */
    private function loadLsappCourses() {
        $courses = fopen($this->coursesFilePath, 'r');
        $courseHeaders = fgetcsv($courses);
        
        while ($row = fgetcsv($courses)) {
            // Store ItemCode as the key for easy lookup
            $itemCode = $row[1]; // Assuming ItemCode is in column 1
            $this->lsappCourses[$itemCode] = $row;
        }
        
        fclose($courses);
    }
    
    /**
     * Import new classes from ELM
     * 
     * @return array Array of imported classes
     */
    public function importNewClasses() {
        $elm = fopen($this->elmFilePath, 'r');
        $this->elmHeaders = fgetcsv($elm);
        
        while ($elmRow = fgetcsv($elm)) {
            $itemCode = $elmRow[1]; // "Class" field in elm.csv is the ItemCode
            
            // Skip if class already exists in LSApp
            if (isset($this->lsappClasses[$itemCode])) {
                continue;
            }
            
            // Check if the course exists in courses.csv
            if (isset($this->lsappCourses[$itemCode])) {
                $courseData = $this->lsappCourses[$itemCode];
                $newClass = $this->createNewClassFromElmAndCourse($elmRow, $courseData);
                
                // Add to classes array (will be saved later)
                $this->lsappClasses[$itemCode] = $newClass;
                
                // Track for reporting
                $this->importedCount++;
                $this->importedClasses[] = [
                    'itemCode' => $itemCode,
                    'courseName' => $courseData[2], // Assuming name is in column 2
                    'startDate' => $elmRow[2], // Assuming start date is in column 2
                    'endDate' => $elmRow[3], // Assuming end date is in column 3
                    'status' => $elmRow[5],
                    'enrolled' => intval($elmRow[8]) + intval($elmRow[16]),
                    'reserved' => $elmRow[9],
                    'pending' => $elmRow[10],
                    'waitlist' => $elmRow[11],
                    'dropped' => $elmRow[12]
                ];
            }
        }
        
        fclose($elm);
        
        // Save the updated classes.csv
        $this->saveClassesFile();
        
        return $this->importedClasses;
    }
    
    /**
     * Create a new class entry from ELM and Course data
     * 
     * @param array $elmRow The ELM class data
     * @param array $courseData The course data
     * @return array The new class entry
     */
    private function createNewClassFromElmAndCourse($elmRow, $courseData) {
        // Create a new row with the same structure as classes.csv
        $newClass = array_fill(0, count($this->classesHeaders), ''); // Initialize empty array
        
        // Generate a unique ID for the new class
        $newClass[0] = uniqid('cls_');
        
        // Fill in data from ELM
        $newClass[1] = $elmRow[5]; // Status
        $newClass[7] = $elmRow[1]; // ItemCode
        $newClass[8] = $elmRow[2]; // Start Date
        $newClass[9] = $elmRow[3]; // End Date
        
        // Enrollment numbers
        $newClass[18] = intval($elmRow[8]) + intval($elmRow[16]); // Enrolled + In Progress
        $newClass[19] = $elmRow[9]; // Reserved
        $newClass[20] = $elmRow[10]; // Pending
        $newClass[21] = $elmRow[11]; // Waitlist
        $newClass[22] = $elmRow[12]; // Dropped
        
        // Fill in data from Course
        $newClass[2] = $courseData[0]; // Course ID
        $newClass[6] = $courseData[2]; // Course Name
        
        // Additional fields can be set based on your specific needs
        $newClass[3] = date('Y-m-d H:i:s'); // Created Date
        $newClass[4] = date('Y-m-d H:i:s'); // Modified Date
        $newClass[5] = 1; // Active flag (assuming 1 means active)
        
        return $newClass;
    }
    
    /**
     * Save updated LSApp classes back to CSV
     */
    private function saveClassesFile() {
        $classesFile = fopen($this->classesFilePath, 'w');
        
        // Add the headers
        fputcsv($classesFile, $this->classesHeaders);
        
        // Write all classes
        foreach ($this->lsappClasses as $fields) {
            fputcsv($classesFile, $fields);
        }
        
        fclose($classesFile);
    }
    
    /**
     * Get the count of imported classes
     * 
     * @return int Count of imported classes
     */
    public function getImportedCount() {
        return $this->importedCount;
    }
    
    /**
     * Get the list of imported classes
     * 
     * @return array Imported classes data
     */
    public function getImportedClasses() {
        return $this->importedClasses;
    }
}

/**
 * UI Helper class to handle display functions
 */
class ImportUI {
    /**
     * Render the imported class list
     * 
     * @param array $importedClasses List of imported classes
     * @return string HTML output
     */
    public function renderImportedClassList($importedClasses) {
        if (empty($importedClasses)) {
            return '<div class="alert alert-info">No new classes were imported.</div>';
        }
        
        $output = '<ul class="list-group">';
        
        foreach ($importedClasses as $class) {
            $output .= $this->renderImportedClassItem($class);
        }
        
        $output .= '</ul>';
        return $output;
    }
    
    /**
     * Render a single imported class item
     * 
     * @param array $class Class data
     * @return string HTML output
     */
    private function renderImportedClassItem($class) {
        $output = '<li class="list-group-item">';
        $output .= '<strong>' . $class['courseName'] . '</strong><br>';
        $output .= 'Start Date: ' . goodDateLong($class['startDate'], '') . '<br>';
        $output .= 'End Date: ' . goodDateLong($class['endDate'], '') . '<br>';
        $output .= $class['itemCode'] . ' IMPORTED.';
        $output .= '<div class="alert alert-info">';
        $output .= 'Status: ' . $class['status'] . '<br>';
        $output .= 'Enrolled: ' . $class['enrolled'] . '<br>';
        $output .= 'Reserved: ' . $class['reserved'] . '<br>';
        $output .= 'Pending: ' . $class['pending'] . '<br>';
        $output .= 'Waitlist: ' . $class['waitlist'] . '<br>';
        $output .= 'Dropped: ' . $class['dropped'] . '<br>';
        $output .= '</div>';
        $output .= '</li>';
        
        return $output;
    }
}

// Main execution
$importer = new ElmClassImporter();
$ui = new ImportUI();

// Only process if initialization is successful
$initSuccess = $importer->initialize();
$importedClasses = [];

if ($initSuccess) {
    $importedClasses = $importer->importNewClasses();
}
?>

<?php getHeader() ?>

<title>ELM Class Import</title>

<style>
.upcount {
    font-size: 30px;
    margin: 30px 0;
}
</style>
<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-12">
<?php include('templates/admin-nav.php') ?>
</div>
<div class="col-md-8">
<h2>ELM - LSApp Class Importer</h2>
<?php if ($initSuccess): ?>
    <div class="alert alert-success">Import Process Completed</div>
    <?php if(isAdmin()): ?>
        <div class="card">
            <div class="card-header">
                <h3>Imported Classes</h3>
            </div>
            <div class="card-body">
                <?php echo $ui->renderImportedClassList($importedClasses); ?>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-danger">Import Failed - Backup Error</div>
<?php endif; ?>
</div>
<div class="col-md-4">
    <div class="card">
        <div class="card-header">
            <h3>Summary</h3>
        </div>
        <div class="card-body">
            <h4><span class="badge text-bg-success"><?= $importer->getImportedCount() ?></span> Classes Imported</h4>
        </div>
    </div>
</div>
</div>
</div>

<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php') ?>